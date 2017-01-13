<?php

require_once ai_cascadepath('includes/plugins/modules/class.module_base.php');
require_once ai_cascadepath(dirname(__FILE__) . '/includes/class.te_repmanager.php');

/**
 * share_links module
 */
class video_manager_module extends module_base
{
	public $mod_system_name = 'video_manager'; // Not static because parent needs to access this
	public $mod_name = 'Video Manager';
	public $mod_description = 'Video Manager';
	public $mod_version = '2.8';
	public $mod_ignore_lock_at_or_before_version = '0.0';

	/**
	 * Called when module is loaded AND is initiated
	 *
	 * @param $settings Array of settings, unrealized from the database
	 */
	public function mod_load_settings( $settings )
	{	}

	/**
	 * mod_upgrade()
	 *
	 * Run any version upgrades.  Only triggered when db version # is out of date when compared to static version # within the module.
	 */
	public function mod_upgrade( $db_version )
	{
		global $AI;

		if ( $this->mod_is_older_version($db_version, '.1') )
		{
			db_query("CREATE TABLE IF NOT EXISTS `video_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL,
  `time` BIGINT( 20 ) NOT NULL DEFAULT '0',
  `live_commentry` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");

			//CREATE PAGE(s)
			$AI->skin->create_dynamicpage(
				'video_manager' //$pagename
				, array('body' => 'includes/modules/video_manager/repmanager.php') //$content
				, array('body' => 'file') //$types
				, 'default' //$skinname = 'default'
				, 'N' //$requires_ssl = 'N'
				, 'en' //$lang = ''
				);

			//ADD PERMISSIONS
			$perm_classes = array('video_manager');
			$perm_groups = array('Website Developers', 'Administrators','Distributors');
			$perm_types = array('ajax','ajax_cmd_inline_edit','ajax_cmd_inline_save','asearch','copy','delete','insert','multidelete','table','update','view');
			$AI->grant_multiple_perms( $perm_classes, $perm_groups, $perm_types, false );
			$perm_groups = array('Users');
			$perm_types = array('ajax','table');
			$AI->grant_multiple_perms( $perm_classes, $perm_groups, $perm_types, false );
			$AI->grant_page_perm( 'video_manager', array('Website Developers','Administrators','Users','Anonymous','Distributors') );

			$this->mod_set_db_version('.1');
		}

		if ( $this->mod_is_older_version($db_version, '2.4') ) {
			//ADD PERMISSIONS
			$perm_classes = array('video_manager');
			$perm_groups = array('Distributors');
			$perm_types = array('ajax','ajax_cmd_inline_edit','ajax_cmd_inline_save','asearch','copy','delete','insert','multidelete','table','update','view');
			$AI->grant_multiple_perms( $perm_classes, $perm_groups, $perm_types, false );
			$AI->grant_page_perm( 'video_manager', array('Distributors') );
		}

	}

	/**
	 * Display help documents
	 */
	public function mod_help()
	{
		echo '<p>This module creates a video_manager manager database.</p>';
	}

	/**
	 * Draw a form to build settings.
	 * @param $fieldstart The starting string to use for input fields
	 * @return null
	 */
	public function mod_settings( $fieldstart )
	{

	}

	/**
	 * Run though the inputed fields
	 *
	 * @see mod_settings
	 * @param $form_items The values submitted by the form drawn in mod_settings()
	 */
	public function mod_settings_validate( $form_items )
	{
		return true;
	}

	////////////////////////////////////////////////////////////////
	// HOOKS ///////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////


};
