<?php
	//email.php
	//(c) 2004 Copyright All Rights Reserved: Joseph D. Frazier, j0zf@ApogeeInvent.com
	//j0zf 2006.6.9 - bug fix : multiple emails sent, cause by $this->to_name = $this->to when to_name was empty
	// JosephL 2009.01.15 :
	//   - Set up to send email mulitple methods, controlable by config setting
	//   - Added SMTP send method

	/*
	CREATE TABLE `email_log` (
	`emailID` int(11) NOT NULL auto_increment,
	`Time_Sent` datetime NOT NULL default '0000-00-00 00:00:00',
	`Email_To` varchar(255) NOT NULL default '',
	`Email_From` varchar(255) NOT NULL default '',
	`Email_Subject` varchar(255) NOT NULL default '',
	`Email_Body` text NOT NULL,
	PRIMARY KEY  (`emailID`)
	) TYPE=MyISAM;
	*/

	class C_email
	{
		var $to = '';
		var $to_name = '';
		var $to_lead_id = 0;
		var $from = '';
		var $from_name = '';
		var $subject = '';
		var $body = '';
		var $return_path = '';
		var $reply_to = '';
		var $crlf = "\r\n";
		var $crlftab = "\r\n\t";

		var $_attachments = array();
		var $_headers = array();
		var $_mime_boundry = '';

		var $send_method = '';
		var $send_email_using = '';

		// SMTP Specific Vars
		var $smtp_conn = null;
		var $smtp_host = null;
		var $smtp_port = 25;
		var $smtp_auth = false;
		var $smtp_user = '';
		var $smtp_password = '';
		var $smtp_timeout = 30;

		private $error;

		public function get_error() { return $this->error; }

		function C_email()
		{
			global $AI;

			if( defined('AI_CRLF') )
			{
				$this->crlf = AI_CRLF;
				$this->crlftab = AI_CRLF . "\t";
			}

			if( defined('AI_SMTP_SERVER') && AI_SMTP_SERVER != '' ){ ini_set('SMTP', AI_SMTP_SERVER ); }
			if( defined('AI_SMTP_PORT') && AI_SMTP_PORT != '' ){ ini_set('smtp_port', AI_SMTP_PORT ); }
			if( defined('AI_SMTP_USER') && AI_SMTP_USER != '' ){ ini_set('sendmail_from', AI_SMTP_USER ); }

			$this->_mime_boundry = AI_SYS_IDENTIFIER . '__{' . md5(time()) . '}__';

			$this->add_header( 'MIME-Version', "1.0" );
			$this->add_header( 'Content-Type', 'text/plain; charset=us-ascii' );
			$this->add_header( 'X-Priority', '3' );
			//$this->add_header( 'X-MSMail-Priority', 'Normal' ); // Disabled, Philip - 04/30/2014
			$this->add_header( 'X-Mailer', 'PHP/' . phpversion() );

			// Determine which method to send (default to sendmail for backwards compatability)
			$this->send_method =  (defined('AI_MAIL_SEND_METHOD') && AI_MAIL_SEND_METHOD != '') ? AI_MAIL_SEND_METHOD : 'sendmail';
			$this->send_email_using = (@$AI->get_setting('send_email_default')!='')? $AI->get_setting('send_email_default'):'sendmail';

			// Additinal Settings that are required to be set depending on send method
			switch ($this->send_method) {
				case 'sendmail':
					// Nothing additional (backward compatiable)
					break;

				case 'smtp':
					// From config settings
					$this->smtp_host = (defined('AI_SMTP_SERVER') && AI_SMTP_SERVER != '') ? AI_SMTP_SERVER : null;
					$this->smtp_port = (defined('AI_SMTP_PORT') && AI_SMTP_PORT != '') ? AI_SMTP_PORT : 25;
					$this->smtp_timeout = (defined('AI_SMTP_TIMEOUT') && AI_SMTP_TIMEOUT != '') ? AI_SMTP_TIMEOUT : 30; // default to 30 seconds

					// IF external smtp server requires authorization
					$this->smtp_auth = (defined('AI_SMTP_AUTH') && AI_SMTP_AUTH != '') ? AI_SMTP_AUTH : false;
					if($this->smtp_auth) {
						$this->smtp_user = (defined('AI_SMTP_USER') && AI_SMTP_USER != '') ? AI_SMTP_USER : '';
						$this->smtp_password = (defined('AI_SMTP_BLURP') && AI_SMTP_BLURP != '') ? AI_SMTP_BLURP : '';
					}
					break;
			}
		}

		function encode_mimeheader($string, $charset=null, $linefeed="\r\n") {
				if (!$charset)
						$charset = mb_internal_encoding();

				$start = "=?$charset?B?";
				$end = "?=";
				$encoded = '';

				/* Each line must have length <= 75, including $start and $end */
				$length = 75 - strlen($start) - strlen($end);
				/* Average multi-byte ratio */
				$ratio = mb_strlen($string, $charset) / strlen($string);
				/* Base64 has a 4:3 ratio */
				$magic = $avglength = floor(3 * $length * $ratio / 4);

				for ($i=0; $i <= mb_strlen($string, $charset); $i+=$magic) {
						$magic = $avglength;
						$offset = 0;
						/* Recalculate magic for each line to be 100% sure */
						do {
								$magic -= $offset;
								$chunk = mb_substr($string, $i, $magic, $charset);
								$chunk = base64_encode($chunk);
								$offset++;
						} while (strlen($chunk) > $length);
						if ($chunk)
								$encoded .= ' '.$start.$chunk.$end.$linefeed;
				}
				/* Chomp the first space and the last linefeed */
				$encoded = substr($encoded, 1, -strlen($linefeed));

				return $encoded;
		}

		function send( $bool_logit = true )
		{
			//ret: true => success, false => fail
			$ret = false;
			$to = '';

			//lookup the lead_id if it's still 0
			if($this->to_lead_id==0 && $this->to!='') $this->to_lead_id = util_get_lead_id_from_email($this->to);

			//ANTI-SPAM - DONT ALLOW \r \n or \t's IN SOME HEADER FIELDS
			$this->to = str_replace( "\r", ' ', str_replace( "\n", ' ', str_replace( "\t", ' ', $this->to )));
			$this->to_name = str_replace( "\r", ' ', str_replace( "\n", ' ', str_replace( "\t", ' ', $this->to_name )));
			$this->from = str_replace( "\r", ' ', str_replace( "\n", ' ', str_replace( "\t", ' ', $this->from )));
			$this->from_name = str_replace( "\r", ' ', str_replace( "\n", ' ', str_replace( "\t", ' ', $this->from_name )));
			$this->subject = str_replace( "\r", ' ', str_replace( "\n", ' ', str_replace( "\t", ' ', $this->subject )));

			if($this->subject != htmlentities($this->subject)) {
				$this->subject = $this->encode_mimeheader($this->subject, "UTF-8");
			}

			$bool_die_on_spam = true;

			//ANTI-SPAM Naughty form-injection
			if(preg_match("/MIME-Version/", $this->to )){ if($bool_die_on_spam){die('NO SPAM PLEASE!');} return false; }
			if(preg_match("/MIME-Version/", $this->to_name )){ if($bool_die_on_spam){die('NO SPAM PLEASE!');} return false; }
			if(preg_match("/MIME-Version/", $this->from )){ if($bool_die_on_spam){die('NO SPAM PLEASE!');} return false; }
			if(preg_match("/MIME-Version/", $this->from_name )){ if($bool_die_on_spam){die('NO SPAM PLEASE!');} return false; }
			if(preg_match("/MIME-Version/", $this->subject )){ if($bool_die_on_spam){die('NO SPAM PLEASE!');} return false; }

			//PREPARE THE TO: AND FROM: HEADERS
			if( $this->to_name != '' )
			{
				$this->to_name = $this->to;
				$to = '"' . str_replace( '"', '', $this->to_name ) . '" <' . $this->to . '>';
			}
			else
			{
				$to = $this->to;
			}

			if( $this->from_name != '' )
			{
				$this->add_header( 'From', '"' . str_replace( '"', '', $this->from_name ) . '" <' . $this->from . '>' );
			}
			else
			{
				$this->add_header( 'From', $this->from );
			}

			foreach (array('to'=>trim($to)
						,'from'=>trim($this->from)
						,'subject'=>trim($this->subject )
						,'body'=>trim($this->body) )
				as $key => $value)
			{
				if($value == '')
				{
					$this->error = "Email '{$key}' cannot be empty.";
					return false;
				}
			}

			if( $bool_logit ) { // Add item to log
				$this->_email_log( $to, $this->from, $this->subject, $this->body );
			}

			switch ($this->send_method) {
				case 'sendmail':
					return $this->send_sendmail($to);
					break;

				case 'smtp':
					return $this->send_smtp($to);
					break;

				default:
					return false;
					break;
			}
		}

		/**
		 * Sent using SMTP on a socket connection
		 * ~ JosephL 2009.01.15
		 */
		function send_smtp( $to )
		{
			// Set true to echo HTML message
			$debug = false;

			// Some standard header items that need to be added
			$this->add_header('To', $this->to);
			$this->add_header('Subject', $this->subject);
			$this->add_header('Return-Path', trim($this->from));

			if($debug) echo '<br>--------------<br>';

			// Connect
			$this->smtp_conn = fsockopen($this->smtp_host,    	 # the host of the server
		                                 $this->smtp_port,     # the port to use
		                                 $errno,   				 # error number if any
		                                 $errstr,  				 # error message if any
		                                 $this->smtp_timeout); # give up after ? secs
			# get any announcement stuff
			$announce = $this->get_lines();
			if($debug) echo 'Announce: ';
			if($debug) echo $announce.'<br>';
			if($debug) echo '<br>--------------<br>';

			if(empty($this->smtp_conn)) {
				// Could not connect
				if($debug) echo 'Can not connect: '.$errno.' '.$errstr;
				return false;
			}

			// HELLO (or EHLLO)
			if(!fputs($this->smtp_conn, "EHLO " . $this->smtp_host . $this->crlf) ) {
				if(!fputs($this->smtp_conn, "HELO " . $this->smtp_host . $this->crlf)) {
					return false;
				}
			}
	    	$results = $this->get_lines();
			$code = (int)substr($results,0,3);
			if($debug) echo 'HELLO RESULTS: '.$results.'<br>';
			if($debug) echo '<br>--------------<br>';

			if($code != 250) {
				if($debug) echo 'EHLO/HELO not accepted from sever';
				return false;
			}

			// AUTH (if requried)
			if($this->smtp_auth) {
				fputs($this->smtp_conn,"AUTH LOGIN" . $this->crlf);
				$results = $this->get_lines();
				$code = (int)substr($results,0,3);

				if($debug) echo 'AUTH RESULTS: '.$results.'<br>';
				if($debug) echo '<br>--------------<br>';

				if($code != 334) {
					if($debug) echo 'AUTH not accepted from server';
					return false;
				}

				// Username
				fputs($this->smtp_conn, base64_encode($this->smtp_user) . $this->crlf);
				$results = $this->get_lines();
				$code = (int)substr($results,0,3);

  				if($debug) echo 'USNAME RESULTS: '.$results.'<br>';
				if($debug) echo '<br>--------------<br>';

				if($code != 334) {
					if($debug) echo 'Username not accepted from server';
					return false;
				}

				// Password
				fputs($this->smtp_conn, base64_encode($this->smtp_password) . $this->crlf);
				$results = $this->get_lines();
				$code = (int)substr($results,0,3);

  				if($debug) echo 'PASSWORD RESULTS: '.$results.'<br>';
				if($debug) echo '<br>--------------<br>';
				if($code != 235) {
					if($debug) echo 'Password not accepted from server';
					return false;
				}
			}

			// MAIL FROM
			fputs($this->smtp_conn,"MAIL FROM: " . $this->from . "" . $this->crlf);
	    	$results = $this->get_lines();
			$code = (int)substr($results,0,3);
			if($debug) echo 'MAIL FROM : '.$results.'<br>';
			if($debug) echo '<br>--------------<br>';
			if($code != 250) {
				if($debug) echo 'MAIL not accepted';
				return false;
			}

			// Recipients
			fputs($this->smtp_conn,"RCPT TO: " . $this->to . "" . $this->crlf);
	    	$results = $this->get_lines();
			$code = (int)substr($results,0,3);
			if($debug) echo 'RCPT: '.$results.'<br>';
			if($debug) echo '<br>--------------<br>';
			if($code != 250 && $code != 251) {
				if($debug) echo 'RCPT not accepted';
				return false;
			}

			// DATA (actual message)
			fputs($this->smtp_conn,"DATA" . $this->crlf);
	    	$results = $this->get_lines();
			$code = (int)substr($results,0,3);
			if($debug) echo 'DATA : '.$results.'<br>';
			if($debug) echo '<br>--------------<br>';
			if($code != 354) {
				if($debug) echo 'DATA command not accepted';
				return false;
			}

			// SERVER NOW READY TO RECIEVE DATA!
			// Send the header line by line
			foreach( $this->_headers as $n => $v )
			{
				if( $v != '' ) {
					fputs($this->smtp_conn,$n . ': ' . $v . $this->crlf);
				}
			}

			// Now put the actual body message
			// RFC 821 says we should not send more than 1000 bytes, included CRLF
			// Create array of 998 characaters strings
			$msg = $this->body;
			$lines = str_split($msg, 998);

			foreach ($lines as $l) {
				// TAKE CARE OF VERY RARE CHANCE THAT SPLIT LINE IS JUST A . (which would trigger end of data)
				if(strlen($l) == 1 && $l == '.') { $l = '. '; }
				fputs($this->smtp_conn, $l . $this->crlf);
			}

			// ANY MINE ATTACHMENTS
			if( count($this->_attachments) > 0 )
			{
				if( $this->_attachments[0]['data'] == '' ) {
					$this->_attachments[0]['data'] = $this->body;
				}

				$mime = '';
				foreach( $this->_attachments as $att )
				{
					$mime .= $this->crlf . '--' . $this->_mime_boundry . $this->crlf;
					foreach( $att['headers'] as $n => $v )
					{
						if( $v != '' ){ $mime .= $n . ': ' . $v . $this->crlf; }
					}
					$mime .= $this->crlf . $att['data'] . $this->crlf;
				}

				$mime .= $this->crlf . '--' . $this->_mime_boundry . '--' . $this->crlf;

				// NOW SEND TO THE SERVER, IN 998 CHUNCKS
				$lines = str_split($mine, 998);
				foreach ($lines as $l) {
					// TAKE CARE OF VERY RARE CHANCE THAT SPLIT LINE IS JUST A . (which would trigger end of data)
					if(strlen($l) == 1 && $l == '.') { $l = '. '; }
					fputs($this->smtp_conn, $l . $this->crlf);
				}
			}

			// Lets host know DATA is finished, and message should be sent
			fputs($this->smtp_conn, '.' . $this->crlf);

			// Properly shut down connection
			fputs($this->smtp_conn, 'QUIT' . $this->crlf);
			fclose($this->smtp_conn);

			// PAST ALL THE RETURN FALSE...
			return true;
		}

		/**
		 * send using the php mail function (sendmail)
		 */
		function send_sendmail($to)
		{
			global $AI;

			if( count($this->_attachments) > 0 )
			{

				if( $this->_attachments[0]['data'] == '' )
				{
					$this->_attachments[0]['data'] = $this->body;
				}

				$mime = '';

				foreach( $this->_attachments as $att )
				{
					$mime .= $this->crlf . '--' . $this->_mime_boundry . $this->crlf;
					foreach( $att['headers'] as $n => $v )
					{
						if( $v != '' ){ $mime .= $n . ': ' . $v . $this->crlf; }
					}
					$mime .= $this->crlf . $att['data'] . $this->crlf;
				}

				$mime .= $this->crlf . '--' . $this->_mime_boundry . '--' . $this->crlf;

				//SEND EMAIL WITH MIME ATTACHMEN
				$pp_api_user = @$AI->get_setting('postalparrot_api');
				$pp_api_hash = @$AI->get_setting('postalparrot_hash');

				if( $this->send_email_using=='postalparrot' && !empty($pp_api_user) && !empty($pp_api_hash) ) {
					// Send via Postal Parrot
					require_once ( ai_cascadepath('includes/plugins/postalparrot_client/class.postalparrot_client.php') );

					$postalparrot = new C_postalparrot_client();
					$data = array(
						"from" => $this->from
						, "subject" => $this->subject
						, "html" => $this->body
						, "to" => $to
						, "lead_id" => $this->to_lead_id
						, "name" => 'simple_email_' . util_rand_string(40, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
						, "priority" => "10"
					);

					$postalparrot->add_data(array('cmd' => 'add_email_and_creative', 'data' => $data));
					$postalparrot->send();
					$ret = true;
				} else {

					$headers = $this->determine_headers();

					$ret = mail( $to, $this->subject, $this->body . $this->crlf . $this->crlf . $mime, $headers, (isset($this->return_path) && trim(@$this->return_path.'') != '') ? '-f' . $this->return_path : '' );
				}
			}
			else
			{
				//SEND EMAIL WITH NO ATTACHMENTS
				$pp_api_user = @$AI->get_setting('postalparrot_api');
				$pp_api_hash = @$AI->get_setting('postalparrot_hash');

				if($this->send_email_using=='postalparrot' && !empty($pp_api_user) && !empty($pp_api_hash) ) {
					// Send via Postal Parrot
					require_once ( ai_cascadepath('includes/plugins/postalparrot_client/class.postalparrot_client.php') );

					$postalparrot = new C_postalparrot_client();
					$data = array(
						"from" => $this->from
						, "subject" => $this->subject
						, "html" => $this->body
						, "to" => $to
						, "lead_id" => $this->to_lead_id
						, "name" => 'simple_email_' . util_rand_string(40, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
						, "priority" => "10"
					);

					$postalparrot->add_data(array('cmd' => 'add_email_and_creative', 'data' => $data));
					$postalparrot->send();
					$ret = true;
				} else {

					$headers = $this->determine_headers();

					$ret = mail( $to, $this->subject, $this->body, $headers );
				}
			}

			return $ret;
		}

		function determine_headers()
		{
			global $AI;

			// Set the reply-to to the from email address, unless it is explicitly set

			// Since the reply-to is set, just use the from as expected and the reply-to as expected
			if($this->reply_to . '' != "") {
				$this->_headers['Reply-To'] = $this->reply_to;
				$this->_headers['From'] = $this->from;

			// Otherwise, build the headers using site settings
			} else {
				$this->_headers['Reply-To'] = $this->from;

				$ai_http_domain = preg_replace( '/^www\./', '', parse_url(AI_HTTP_URL, PHP_URL_HOST));

				// Check to see if there is a site setting or use this setting
				$this->_headers['From'] = (@$AI->get_setting('default_email_from') != "" ? $AI->get_setting('default_email_from') : "noreply@" . $ai_http_domain);
			}

			// Now that we have everything, let's build the array
            if(!empty($this->from)){
                $this->_headers['From'] = $this->from;
                if(!empty($this->from_name)){
                    $this->_headers['From'] =  $this->from_name . ' <' . $this->from . '>';
                }
            }


			$headers = '';
			foreach( $this->_headers as $n => $v )
			{
				if( $v != '' ){ $headers .= $n . ': ' . $v . $this->crlf; }
			}

			return $headers;
		}

		function add_header( $header_name, $header_value )
		{
			$this->_headers[ $header_name ] = $header_value;
		}

		function attach_file( $fname )
		{
			$this->attach_data( $this->_file_data($fname), array(
					'Content-Type' => 'application/octet-stream;' . $this->crlftab . 'name="' . basename($fname) . '"'
					,'Content-Transfer-Encoding' => 'base64'
					,'Content-Description' => '"Attached File"'
				)
			);
		}

		function attach_image( $fname )
		{
			$this->attach_data( $this->_file_data($fname), array(
					'Content-Type' => 'image/' . substr( $fname, strrpos( $fname, '.' )+1 ) . ';' . $this->crlftab . 'name="' . basename($fname) . '"'
					,'Content-Transfer-Encoding' => 'base64'
					,'Content-Description' => '"Attached Image"'
					,'Content-Disposition' => 'inline;' . $this->crlftab . 'filename="' . basename($fname) . '"'
				)
			);
		}

		function attach_html( $html )
		{
			$this->_prepare_for_mime();

			$this->_attachments[0] = array(
				'data' => quoted_printable_encode($html)
				,'headers' => array(
					'Content-Type' => 'text/html; charset="utf-8"' /* "us-ascii" */
					,'Content-Transfer-Encoding' => 'quoted-printable' /* '7bit */
				)
			);
		}

		function attach_data( $data, $headers )
		{
			$this->_prepare_for_mime();

			$this->_attachments[] = array(
					'data' => chunk_split(base64_encode($data))
					,'headers' => $headers
			);
		}

		function _file_data( $fname )
		{
			$fin = fopen($fname, 'r');

			if( $fin )
			{
				$data = fread($fin, filesize($fname));
				fclose($fin);
				return $data;
			}
			else
			{
				return '';
			}
		}

		function _prepare_for_mime()
		{
			$this->add_header( 'Content-Type', 'multipart/mixed;' . $this->crlftab . 'boundary="' . $this->_mime_boundry . '";' );

			if( count( $this->_attachments ) < 1 )
			{
				//add an empty slot for the text "body"
				$this->_attachments[] = array(
					'data' => ''
					,'headers' => array(
						'Content-Type' => 'text/plain; charset=us-ascii'
						,'Content-Transfer-Encoding' => '7bit'
					)
				);
			}
		}

		function _email_log( $Email_To, $Email_From, $Email_Subject, $Email_Body )
		{
			foreach ( $this->_headers as $n => $v )
			{
				if ( preg_match('/^b?cc$/i', trim($n)) )
				{
					$Email_To .= ', ' . $n . ':' . $v;
				}
			}

			db_query("INSERT INTO email_log
				SET Time_Sent = '" . db_in( date('Y-m-d H:i:s') ) . "'
				, Email_To = '" . db_in($Email_To) . "'
				, Email_From = '" . db_in($Email_From) . "'
				, Email_Subject = '" . db_in($Email_Subject) . "'
				, Email_Body = '" . db_in($Email_Body) . "'
			;");
		}

		function get_lines() {
	    $data = "";
	    while($str = @fgets($this->smtp_conn,515)) {
	      $data .= $str;

	      # if the 4th character is a space then we are done reading
	      # so just break the loop
	      if(substr($str,3,1) == " ") { break; }

			if($str == '') {break;}
	    }
	    return $data;
	  }
	}

	// str_split is PHP 5 only, make PHP 4 compatiable
	// Found from comments on php.net
	if(!function_exists('str_split')) {
	    function str_split($string,$string_length=1) {
	        if(strlen($string)>$string_length || !$string_length) {
	            do {
	                $c = strlen($string);
	                $parts[] = substr($string,0,$string_length);
	                $string = substr($string,$string_length);
	            } while($string !== false);
	        } else {
	            $parts = array($string);
	        }
	        return $parts;
	    }
	}

	if(!function_exists('quoted_printable_encode')) {
		function quoted_printable_encode($input, $line_max = 75) {
			 $hex = array('0','1','2','3','4','5','6','7',
															'8','9','A','B','C','D','E','F');
			 $lines = preg_split("/(?:\r\n|\r|\n)/", $input);
			 $linebreak = "=\r\n"; // Changed linebreak method from =0D=0A=\r\n, Philip - 9/22/2014
			 /* the linebreak also counts as characters in the mime_qp_long_line
				* rule of spam-assassin */
			 $line_max = $line_max - strlen($linebreak);
			 $escape = "=";
			 $output = "";
			 $cur_conv_line = "";
			 $length = 0;
			 $whitespace_pos = 0;
			 $addtl_chars = 0;

			 // iterate lines
			 for ($j=0; $j<count($lines); $j++) {
				 $line = $lines[$j];
				 $linlen = strlen($line);

				 // iterate chars
				 for ($i = 0; $i < $linlen; $i++) {
					 $c = substr($line, $i, 1);
					 $dec = ord($c);

					 $length++;

					 if ($dec == 32) {
							// space occurring at end of line, need to encode
							if (($i == ($linlen - 1))) {
								 $c = "=20";
								 $length += 2;
							}

							$addtl_chars = 0;
							$whitespace_pos = $i;
					 } elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) {
							$h2 = floor($dec/16); $h1 = floor($dec%16);
							$c = $escape . $hex["$h2"] . $hex["$h1"];
							$length += 2;
							$addtl_chars += 2;
					 }

					 // length for wordwrap exceeded, get a newline into the text
					 if ($length >= $line_max) {
						 $cur_conv_line .= $c;

						 // read only up to the whitespace for the current line
						 $whitesp_diff = $i - $whitespace_pos + $addtl_chars;

						/* the text after the whitespace will have to be read
						 * again ( + any additional characters that came into
						 * existence as a result of the encoding process after the whitespace)
						 *
						 * Also, do not start at 0, if there was *no* whitespace in
						 * the whole line */
						 if (($i + $addtl_chars) > $whitesp_diff) {
								$output .= substr($cur_conv_line, 0, (strlen($cur_conv_line) -
															 $whitesp_diff)) . $linebreak;
								$i =  $i - $whitesp_diff + $addtl_chars;
							} else {
								$output .= $cur_conv_line . $linebreak;
							}

						$cur_conv_line = "";
						$length = 0;
						$whitespace_pos = 0;
					} else {
						// length for wordwrap not reached, continue reading
						$cur_conv_line .= $c;
					}
				} // end of for

				$length = 0;
				$whitespace_pos = 0;
				$output .= $cur_conv_line;
				$cur_conv_line = "";

				if ($j<=count($lines)-1) {
					$output .= $linebreak;
				}
			} // end for

			return trim($output);
		} // end quoted_printable_encode
	}