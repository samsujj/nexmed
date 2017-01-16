<?php

/**
 * System Emails
 *  Send out system generated emails while giving the admins the ability to customize the email message
 *  Customization of emails done using table edit.  This class exists to send out the emails.
 * @author: JosephL
 */

require_once( ai_cascadepath('includes/core/classes/email.php') );
require_once( ai_cascadepath('includes/core/classes/error_base.php') );
require_once( ai_cascadepath('includes/plugins/postalparrot_client/class.postalparrot_client.php') );
require_once( ai_cascadepath('includes/plugins/dynamic_areas/includes/class.te_dynamic_areas.php') );

class C_system_emails extends C_error_base
{
	const DEFAULT_FROM_AT = 'noreply@'; // domain will be parsed automatically based on server
	const VAR_PREFIX = '[[';
	const VAR_SUFFIX = ']]';

	public $encode_vars = true;

	protected $db;
	protected $vars;
	protected $defaults;
	protected $use_defaults;
	protected $from;
	protected $from_name;
	protected $subject;
	protected $msg;

	private static $system_emails_arr;
	private $to_cache = array();
	private $other_headers_cache = array();

	/**
	 * Creates a new system eemail
	 * Performs external maintenance and relationships, e.g. dynamic areas for the message body
	 * @param  string $title   The keyed title of the email
	 * @param  string $subject The subject of the email, can include merge codes
	 * @param  string $message The body of the email, can be HTML, and can include merge codes
	 * @param  array  $vars    A single dimensional array of available merge codes
	 * @param  string $from    (Optional) A hard-value From: header
	 * @return int|string      Returns the primary key if successful, otherwise a string message describing a DB error
	 */
	public static function create( $title, $subject, $message, array $vars, $from = null, $lang = null )
	{
		global $AI;

		$now = date('Y-m-d H:i:s');
		$from = $from === null ? '' : $from;
		$lang = $lang === null ? $AI->get_default_lang() : $lang;

		$message_da_name = 'system_emails_email_msg_' . util_rand_string(40);

		$vars_parsed = array();
		foreach ( $vars as $var )
		{
			$_v = array($var, '', '');
			$vars_parsed[] = $_v;
		}

		$data = array
			( 'title'              => trim($title)
			, 'email_from'         => trim($from)
			, 'email_subject'      => trim($subject)
			, 'email_msg'          => $message_da_name
			, 'vars'               => serialize($vars_parsed)
			, 'delivery_method'    => 'default'
			, 'date_last_modified' => $now
			, 'date_added'         => $now
			);
		$res = db_perform('system_emails', $data, 'insert');
		if ( !$res )
		{
			return db_error();
		}

		$data = array
			( 'name'       => $message_da_name
			, 'mode'       => 'wysiwyg'
			, 'content'    => $message
			, 'lang'       => $lang
			, 'created_on' => $now
			, 'saved_on'   => $now
			);
		$res = db_perform('dynamic_areas', $data, 'insert');
		if ( !$res )
		{
			return db_error();
		}
		return (int) db_insert_id();
	}

	/**
	 * CONSTRUCTOR
	 * @param: vars, associate array of variable-replacement pairs
	 * @param: system_name, auto load a template via the system_name (system_emails.title)
	 */
	public function __construct( $system_name )
	{
		$this->clear_vars();
		$this->load($system_name, 'title');
	}

	/**
	 * returns system_emails emails options for select input
	 * @param  string $selected selected option currently saved
	 * @return string           html, list of options
	 */
	public static function get_system_emails_select_options($selected)
	{
		global $AI;
		$disabled = false;
		if(util_mod_enabled('postal_parrot') == 1
				&& $AI->get_setting("postalparrot_api") != ""
				&& $AI->get_setting("postalparrot_hash") != ""
				) { $allow_postal_parrot = true; }
		else { $allow_postal_parrot = false; }

		$sql = "SELECT email_id, title, delivery_method FROM system_emails ORDER BY title ASC";

		if(empty(self::$system_emails_arr))
		{
			self::$system_emails_arr = $AI->db->GetAll($sql);
		}
		$results = self::$system_emails_arr;

		$options = "<option value='none'>None</option>";
		foreach ($results as $key => $value) {
			$options .= "<option value='{$value['title']}' ".($selected == $value['title'] ? 'selected' : '' ) . ($allow_postal_parrot == false && $value['delivery_method'] == 'postal parrot' ? " disabled": "") .">{$value['title']}</option>";
		}

		return $options;
	}

	/**
	 * An static abstraction method that will send a system email.  Used for quick and easy system email messaging.
	 * @param   string  $system_name The system emails
	 * @param   array   $vars        The email variables
	 * @param   mixed  $to_email     The email sent to.  Array or string.
	 * @param   string  $from_email  The emails sent from
	 * @return  boolean              True if email was successfully sent
	 * @example C_system_emails::send_system_email('demo', array('first'=>'john','last'=>'smith'), 'felipe@apogeeinvent.conm');
	 * @author  felipe
	 * @date    2015-03-14
	 * @updated 2015-03-16
	 */
	public static function send_system_email($system_name, $vars, $to_email, $from_email = '')
	{
		global $AI;

		$class_name = __CLASS__;
		$obj = new $class_name($system_name);
		$obj->set_vars_array($vars);

		$from_email = ( $from_email != "" ? $from_email : $AI->get_setting('notify_email') );
		$obj->set_from($from_email);

		if(!$obj->send($to_email,array())){
			trigger_error(implode($obj->get_errors(), ', ') );
			return false;
		}
		return true;
	}

	/**
	 * Returns whether a system email was loaded
	 * @return bool
	 */
	public function is_loaded()
	{
		return !empty($this->db['email_id']);
	}

	/**
	 * load the message from the database
	 * @param: id, unique ID to identify the row
	 * @param: mode, (system_name, title, id), system_name/title = text title, id = numeric auto ID
	 */
	public function load( $title_or_id, $mode )
	{
		switch ( $mode )
		{
			case 'title':
			case 'system_name':
				$row = $this->load_by_title($title_or_id);
				if(!$row)
				{
					//expect defaults to be provided. save title for later use,
					//in case we need to insert into the db
					$this->defaults['title'] = $title_or_id;
				}
				break;
			case 'id':
				$row = $this->load_by_id($title_or_id);
				break;
			default:
				$this->error('Could not load row from database, no valid mode provided');
				return;
				break;
		}
		if(isset($row['email_id']) && !$row['is_default'])
		{
			$this->db = $row;
			$this->load_var_defaults();
			//we were able to load this email from the db, and the is_default flag is off,
			//so do not use any values from the defaults array
			$this->use_defaults = false;
		}
		else
		{
			//if not found in the database, expect defaults to be provided later
			$this->use_defaults = true;
			//set to the default delivery method. this can still be changed later with set_default()
			$this->db['delivery_method'] = 'default';
			//this lets us know if we need to insert this email into the db or not
			if(isset($row['is_default'])){ $this->db['is_default'] = $row['is_default']; }
			//check if enabled
			$this->db['enabled'] = $row['enabled'];
		}

		// load global system_email variables (if any)
		if( ($file=ai_cascadepath('includes/plugins/system_emails/global_reps.php'))!='' ) require($file);
	}

	/**
	 * set a variable
	 * @param: name, the variable name (without the var prefix and var suffix)
	 * @param: value, the replacement text
	 */
	public function set_var( $name, $value )
	{
		$this->vars[$name] = $value;
	}
	/**
	 * set all replacements
	 */
	public function set_vars_array( $vars )
	{
		if(!is_array($this->vars)){ $this->vars = array(); }
		$this->vars = array_merge($this->vars, $vars);
		$this->update_system_email_variables();
	}

	/**
	 * sets a default email value. at minimum "email_subject" and "email_msg" should be provided.
	 * "email_from" and "delivery_method" can also be set.
	 * @param: name, the variable name (without the var prefix and var suffix)
	 * @param: value, the replacement text
	 */
	public function set_default( $name, $value )
	{
		$this->defaults[$name] = $value;
	}
	/**
	 * set all default values
	 */
	public function set_defaults_array( $defaults )
	{
		if(!is_array($this->defaults)){ $this->defaults = array(); }
		$this->defaults = array_merge($this->defaults, $defaults);
	}

	/**
	 * clears all variables
	 */
	public function clear_vars()
	{
		$this->vars = array();
	}

	/**
	 * set a manual "From:" email address (Not mandatory)
	 * @param: $email_address, the email address
	 */
    public function set_from( $email_address )
    {
        $this->from = trim($email_address);
    }
    public function set_from_name( $from_name= '' )
    {
        if(!empty($from_name)){
            $this->from_name = trim($from_name);
        }
    }
	public function from( $email_address ) { return $this->set_from($email_address); } // ALIAS

	/**
	 * get the final "From:" email address
	 */
	public function get_from()
	{
		global $AI;

		$from = $this->from;
		if ( empty($from) && isset($this->db['email_from']) )
		{
			$from = $this->db['email_from'] . '';
		}
		if ( trim($from) == '' )
		{
			if ( $AI->get_setting('default_email_domain')!='' )  {
				$from = self::DEFAULT_FROM_AT . $AI->get_setting('default_email_domain') . '';
			}
			else if ( !empty($_SERVER['SERVER_NAME']) ) {
				$from = self::DEFAULT_FROM_AT . str_replace('www.', '', $_SERVER['SERVER_NAME']);
			}
			else if( preg_match('/.*www\.(.*)/',$AI->get_setting('DEFAULT_HTTP_URL'),$matches) ) {
				$domain = trim($matches[1],'/');
				$from = self::DEFAULT_FROM_AT . $domain;
			}
		}
		return $from;
	}

	/**
	 * Used to determine if email is valid to be sent.
	 * @return boolean True if email is good to be sent
	 */
	private function validate()
	{
		$this->load_defaults();
		if($this->db['enabled'] == 0 ) { $this->error('System Email of id '.$this->db['email_id'].' and title '.$this->db['title'].'  is <a href="system_emails?te_mode=update&te_key='.$this->db['email_id'].'" target="_blank">disabled</a>.  It must be enabled to be sent.'); }

		if($this->has_errors())
		{
			return false;
		}

		return true;
	}

	/**
	 * Set email to
	 * @param array $new_to_cache The value to set
	 * @author  felipe
	 * @date 2016-11-08
	 */
	public function set_to_cache($new_to_cache) { if(!in_array($new_to_cache,$this->to_cache)) {$this->to_cache[] = $new_to_cache; } }
	/**
	 * Getter
	 * @return array The value of class property
	 * @author  felipe
	 * @date 2016-11-08
	 */
	public function get_to_cache() { return $this->to_cache; }

	/**
	 * Set email other headers
	 * Can be used to set bcc
	 * @param array $new_other_headers_cache The value to set
	 * @author  felipe
	 * @date 2016-11-08
	 */
	public function set_other_headers_cache(array $new_other_headers_cache)
	{
		foreach ($new_other_headers_cache as $key => $value)
		{
			$key = strtolower($key);
			if(!isset($this->other_headers_cache[$key])) {$this->other_headers_cache[$key] = '';}
			if($value == '' ) {continue;}
			if(util_in_string($value,$this->other_headers_cache[$key])) {continue;}
			$this->other_headers_cache[$key] .= $value . ';';
		}
	}
	/**
	 * Getter
	 * @return array The value of class property
	 * @author  felipe
	 * @date 2016-11-08
	 */
	public function get_other_headers_cache() { return $this->other_headers_cache; }

	/**
	 * Send emails with cached values in the class.
	 * Give the ability to pass this class as dependency and set emails to and other_headers and postpone sending
	 * @return boolean True if email send successfully
	 * @author  felipe
	 * @date   2016-11-08
	 */
	public function send_cache()
	{
		return $this->send($this->to_cache,$this->other_headers_cache);
	}

	/**
	 * send
	 * Send the message to recipient(s)
	 * @param: $to, 1) a single email recipient or 2) an array of recipient addresses or 3) an assoc array of (email=>__,lead_id=>__)
	 * @param: $other_headers, any additional headers in an associative array (optional)
	 * @param: $to_lead_id, if $to is a single email then $to_lead_id is it's lead_id
	 */
	public function send( $to, $other_headers = array(), $to_lead_id=0, $log_email = true )
	{
		// prevent email to be sent if not valid
		if($this->validate() == false)
		{
			return false;
		}

		switch ( $this->db['delivery_method'] )
		{
			case 'default':
				return $this->send_via_default($to, $other_headers, $log_email);
				break;
			case 'postal parrot':
				return $this->send_via_postal_parrot($to, $other_headers, $to_lead_id, $log_email);
				break;
			default:
				$this->error('Could not send message, no valid delivery message specified: "' . $this->db['delivery_method'] . '"');
				break;
		}
		return false;
	}

	/**
	 * log_email
	 * Log the email delivery to the logging table
	 */
	public function log_email($se_id = 0, $email = "")
	{
		global $AI;

		$array = array();
		$array['email_id'] = $se_id;
		$array['email_address'] = (is_array($email)) ? implode(',',$email): trim($email);
		$array['timestamp'] = date("Y-m-d H:i:s");

		$AI->db->Insert('system_emails_log',$array);
	}

	/////////////////////////////////////////////////
	// Private

	protected function send_via_default( $to, $other_headers = array(), $log_email )
	{
		global $AI;
		// Load defaults
		$this->load_defaults();
		// Load dynamic (variable-replacement) content
		$this->load_message();
		$this->load_subject();
		$this->from = $this->get_from();
		// Confirm that subject and message are provided, either from db or defaults

		foreach (array('subject'=>$this->subject, 'message'=>$this->msg, 'to'=>$to) as $key => $value) {
			if(empty($value)) {
				$this->error('Email minimum requirements not met. Email not sent. Missing "'. $key .'"');
				return false;
			}
		}
		/*
		if(empty($this->subject) || empty($this->msg) || empty($to))
		{
			$this->error('Email minimum requirements not met. Email not sent.');
			return false;
		}
		*/

		// Convert the logic for making the images full path urls
		$this->msg = str_replace( '"uploads/dynamic_areas/', '"' . AI_HTTP_URL . 'uploads/dynamic_areas/', $this->msg );
		$this->msg = str_replace( '\'uploads/dynamic_areas/', '\'' . AI_HTTP_URL . 'uploads/dynamic_areas/', $this->msg );
		$this->msg = str_replace( '"http://uploads/dynamic_areas/', '"' . AI_HTTP_URL . 'uploads/dynamic_areas/', $this->msg );
		$this->msg = str_replace( '\'http://uploads/dynamic_areas/', '\'' . AI_HTTP_URL . 'uploads/dynamic_areas/', $this->msg );

		// Construct the email object
		$e = new C_email();
		$e->add_header('Content-Type', 'text/html; charset=utf-8'); // Always send out an HTML email
		$e->from = $this->from;
		$e->from_name = $this->from_name;
		$e->subject = strip_tags($this->subject);
		// Build the body
		$body  = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
		$body .= '<html>';
		$body .= '<head>';
		$body .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
		//$body .= '<title>' . h($e->subject) . '</title>';
		$body .= '</head>';
		$body .= '<body>';
		$body .= $this->msg;
		$body .= '</body>';
		$body .= '</html>';
		// Other headers (currently the array keys are the header names, which may cause some problems, but is not typical)
		if ( count($other_headers) > 0 )
		{
			foreach ( $other_headers as $_n => $_v )
			{
				$e->add_header($_n, $_v);
			}
		}
		// Send the message
		if ( !is_array($to) ) { $to = array($to); }
		foreach ( $to as $email_arr )
		{
			if(!is_array($email_arr)) {
				$email = $email_arr;
				$to_lead_id = 0;
			}
			else if(isset($email_arr['email'])) {
				$email = $email_arr['email'];
				$to_lead_id = $email_arr['lead_id'];
			}

			$email = trim($email);
			if ( preg_match('/@example[.](com|net|org)$/i', $email) ) // RFC 2606 example domains, don't do any processing, but still a success (so log)
			{
				$this->notice('Example email detected: ' . $email . ' (No send / OK)');
				$e->_email_log($email, $e->from, $e->subject, $body);
				continue;
			}
			$e->to = $email;

			$e->attach_html($body);
			$e->body = strip_tags($body);

			if ( !$e->send() )
			{
				$this->error('Could not send email to ' . $email);
			} else {
				if($log_email) {
					$log_email_id = array_key_exists('email_id',$this->db)?$this->db['email_id']:0;
					$this->log_email($log_email_id,$email);
				}
			}
		}
		// Return
		if ( $this->has_errors() )
		{
			return false;
		}
		return true;
	}

	protected function send_via_postal_parrot($to, $other_headers = array(), $to_lead_id=0, $log_email)
	{
		//$to can be an array of emails & lead_ids(optional)
		global $AI;
		// Make sure there are valid API settings
		$pp_api_user = trim(@$AI->get_setting('postalparrot_api') . '');
		$pp_api_hash = trim(@$AI->get_setting('postalparrot_hash') . '');
		if ( empty($pp_api_user) || empty($pp_api_hash) )
		{
			$this->error('Postal Parrot API settings are not set up correctly');
			return false;
		}
		if ( !util_is_module_enabled('postal_parrot') )
		{
			$this->error('Postal Parrot Module is not enabled');
			return false;
		}

		// Parse PP vars
		$options = @unserialize($this->db['delivery_method_options']);
		$creative_name = @$options['delivery_method_options_postal_parrot_creative_name'] . '';
		$list_name = @$options['delivery_method_options_postal_parrot_list_name'] . '';
		// Send the message
		if ( !is_array($to) )
		{
			$to = array($to);
		}
		$this->format_vars();
		$this->from = $this->get_from();
		foreach ( $to as $email_arr )
		{
			if(!is_array($email_arr)) {
				$email = $email_arr;
				$to_lead_id = (count($to)==1)? $to_lead_id:0;
			}
			else if(isset($email_arr['email'])) {
				$email = $email_arr['email'];
				$to_lead_id = $email_arr['lead_id'];
			}

			$pp_data = array
				( 'creative_name' => $creative_name
				//, 'list_name' => $list_name
				, 'lead_id' => ($to_lead_id>0? $to_lead_id:util_get_lead_id_from_email($email))
				, 'vars' => $this->vars
				, 'email_address' => $email
				, 'to' => $email
				, 'from' => $this->from
				, 'headers' => $other_headers
				);
			// Send via Postal Parrot
			$postalparrot = new C_postalparrot_client();
			$postalparrot->add_data(array('cmd' => 'send_creative_to_emails', 'data' => $pp_data));
			$ret = $postalparrot->send();
			/*
			if ( $ret[0]['status'] != 'Success' )
			{
				$err = array
					( 'ret' => $ret
					, 'data' => $pp_data
					);
				$this->error('Could not send email via Postal Parrot to ' . $email . "\r\n" . print_r($err, true));
			}
			*/
			$this->notice(print_r($ret, true));
		}
		// Return
		if ( $this->has_errors() )
		{
			return false;
		}

		if($log_email) {
			$this->log_email($this->db['email_id'],$to);
		}

		return true;
	}

	protected function load_message( $replace = true )
	{
		global $AI;

		$this->msg = '';
		if ( !isset($this->db['email_msg']) )
		{
			$this->error('Could not load message from database.');
			return;
		}

		if(!$this->use_defaults)
		{
			$this->msg = $AI->get_dynamic_area($this->db['email_msg'], 'name', $AI->get_lang(), false);
		}
		else
		{
			//using hard coded defaults, not loaded from db
			$this->msg = $this->db['email_msg'];
		}

		if ( $replace )
		{
			$this->msg = $this->replace_vars($this->vars, $this->msg);
		}
	}

	protected function load_subject( $replace = true )
	{
		$this->subject = '';
		if ( !isset($this->db['email_subject']) )
		{
			$this->error('Could not load subject from database.');
			return;
		}

		$this->subject = $this->db['email_subject'];
		if ( $replace )
		{
			$this->subject = $this->replace_vars($this->vars, $this->subject);
		}
	}

	/**
	 * replace the passed vars in the passed message
	 */
	protected function replace_vars( $vars, $msg )
	{
		foreach ( $vars as $tag => $replacement )
		{
			$full_tag = self::VAR_PREFIX . $tag . self::VAR_SUFFIX;
			if ( $this->encode_vars )
			{
			$msg = str_replace(array($full_tag,strtoupper($full_tag)), nl2br(h($replacement)), $msg);
			}
			else
			{
				$msg = str_replace(array($full_tag, strtoupper($full_tag)), $replacement, $msg);
			}
		}
		/*
		if ( !empty($this->util_translate_placeholders_userID) )
		{
			$msg = util_translate_placeholders($msg, $this->util_translate_placeholders_userID);
		}
		*/
		return $msg;
	}

	/**
	 * In case vars are sent elsewhere (i.e. exported out of this class),
	 * the vars must be formatted correctly
	 */
	protected function format_vars()
	{
		$vars = $this->vars;
		$this->vars = array();
		foreach ( $vars as $tag => $replacement )
		{
			$full_tag = self::VAR_PREFIX . $tag . self::VAR_SUFFIX;
			$this->vars[$full_tag] = $replacement;
		}
	}

	// get the default vars, if they are not set in the passed vars, load the default values
	protected function load_var_defaults()
	{
		$defaults = unserialize($this->db['vars']);

		if ( is_array($defaults) && count($defaults) > 0 )
		{
			foreach ( $defaults as $row_id => $row )
			{
				// $row['0'] == variable
				// $row['1'] == default
				// $row['2'] == description

				if ( !isset($this->vars[$row['0']]) || @trim($this->vars[$row['0']]) == '' )
				{
					// its not set or it is blank, load the default
					$this->vars[$row['0']] = $row['1'];
				}
			}
		}
	}

	//if a system email does not exist in the database, or the is_default flag is on,
	//then we want to use the default email values provided in the code.
	protected function load_defaults()
	{
		if($this->use_defaults && is_array($this->defaults))
		{
			foreach($this->defaults as $key => $value)
			{
				$this->db[$key] = $value;
			}
		}

		//should we insert this as a new row in the system_emails table?
		//is_default not being set indicates we did not find this email in the database.
		//also only insert if we have at least the basic information
		if(!isset($this->db['is_default'])
			&& isset($this->db['title'])
			&& isset($this->db['email_subject'])
			&& isset($this->db['email_msg']))
		{
			$sql = 'INSERT INTO system_emails SET ';
			//title should be set from load(), unless tried to load by id
			$sql .= 'title = "'.db_in($this->db['title']).'", ';
			if(isset($this->db['email_from']))
			{
				$sql .= 'email_from = "'.db_in($this->db['email_from']).'", ';
			}
			$sql .= 'email_subject = "'.db_in($this->db['email_subject']).'", ';
			//create dynamic area for the content
			$da = new C_te_dynamic_areas();
			$da_name = 'system_emails_email_msg_'.util_rand_string(40, '0123456789abcdefghijklmnopqrstuvwxyz');
			$da->create(0, $da_name, $this->db['email_msg']);
			$sql .= 'email_msg = "'.db_in($da_name).'", ';
			//format the vars array. each var row is an array with three values: name, default, description
			$vars = array();
			foreach($this->vars as $key => $value)
			{
				$vars[] = array($key, $value, '');
			}
			$sql .= 'vars = "'.db_in(serialize($vars)).'", ';
			$sql .= 'delivery_method = "'.db_in($this->db['delivery_method']).'", ';
			$sql .= 'enabled = "1", ';
			$sql .= 'is_default = "1", ';
			$now = date('Y-m-d H:i:s');
			$sql .= 'date_last_modified = "'.db_in($now).'", ';
			$sql .= 'date_added = "'.db_in($now).'"';

			db_query($sql);
			$this->email_id = db_insert_id();
			$this->enabled = true;
			$this->is_default = true;
		}
	}

	protected function load_by_title( $title )
	{
		return db_lookup_assoc("SELECT * FROM system_emails WHERE title = '" . db_in($title) . "' LIMIT 1;");
	}

	protected function load_by_id( $id )
	{
		return db_lookup_assoc("SELECT * FROM system_emails WHERE email_id = " . (int) db_in($id) . " LIMIT 1;");
	}


	protected function update_system_email_variables(){

		@$email_vars = unserialize($this->db['vars']);
		$value = '';

		if(!is_array($email_vars))$email_vars=array();
		foreach($email_vars AS $n=>$v)
		{
			foreach($v AS $j=>$k)
			{
				if($k!='')
				{
					$value = $k;
				}
			}
		}

		if($value=='')
		{
			$update_vars = array();
			$counter = 0;
			foreach($this->vars AS $n=>$v)
			{
				if($n!='')
				{
					$update_vars[$counter] = array($n, '', $n);
					$counter++;
				}
			}

			@$update_vars = serialize($update_vars);
			if(array_key_exists('email_id',$this->db))
			{
				db_query("UPDATE system_emails SET vars='".db_in($update_vars)."' WHERE email_id = " . (int) db_in($this->db['email_id']) . " LIMIT 1;");
			}
		}
	}
}
