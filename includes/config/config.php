<?php
	//config.php - Joseph D. Frazier - j0zf@apogeeinvent.com
	//(c) 2004 - 2008 Copyright All Rights Reserved - Apogee Design, Inc. 
	
	global $AI;
	
	// Dynamcially determine the settings... if a local file exists use that for settings, otherwise fall back on settings set inside this file. ~ JosephL 2011.03.01
	// Support for local development version of this code.  Development settings are not SVN controlled.
	$local_settings_file = dirname(__FILE__).'/config.local.php';
	if( file_exists($local_settings_file) ) {
		$AI->set_setting( 'SITE_CONFIG_MODE', 'local' );
	}
	else {
		$AI->set_setting( 'SITE_CONFIG_MODE', 'aicore' );
	}
	
	$AI->set_setting( 'DEBUGGER', false );//set true to output all debug statements -Dwight2006-01-16
	$AI->set_setting( 'TEMP_DOWN_PAGE', 'temp_down.htm' );
	
	// Unique system encryption key (15 random characters)
	$AI->set_setting( 'SITE_BLURB_KEY', 'Y2MyMTdjMWQzOWY' );

	switch( AI_SITE_CONFIG_MODE )
	{
		case 'aicore':
		{
			//DATEBASE
			$AI->set_setting( 'DATABASE_SYSTEM', 'mysql' );
			$AI->set_setting( 'DB_SERVER', 'localhost' );
			$AI->set_setting( 'DB_USERNAME', 'nexmed_dba' );
			$AI->set_setting( 'DB_BLURP', 'YjEzODY1MTc4MTAzMTI4' );
			$AI->set_setting( 'DB_DATABASE', 'nexmed_ai' );
			
			//!!!!!!! ALSO SET RewriteBase IN .htaccess !!!!!!!!!!
			//URLS (Full URL and Path to application with trailing slash)
			//$AI->set_setting( 'DEFAULT_HTTP_URL', 'http://www.nexmedsolutions.com/' );
			$AI->set_setting( 'DEFAULT_HTTP_URL', 'http://mars.apogeehost.com/~nexmed/' );
			$AI->set_setting( 'DEFAULT_HTTPS_URL', 'https://mars.apogeehost.com/~nexmed/' );
      		//!!!!!!! ALSO SET RewriteBase IN .htaccess !!!!!!!!!!
			
			//SSL
			//pageConfig.php will define AI_HTTPS_ON as true/false
			$AI->set_setting( 'USE_HTTPS', true );
			
			//STORE SESSIONS IN THE DATABASE?
			$AI->set_setting( 'USE_DB_SESSIONS', true );
			$AI->set_setting( 'DB_SESSION_LIFE', 3600 * 8 ); // 60sec * 60mins * 6hrs = 21600sec, 3600 = 1hr
			
			//EMAIL - SMTP SETUP
			$AI->set_setting( 'SMTP_SERVER','localhost' );
			$AI->set_setting( 'SMTP_PORT','25' );
			$AI->set_setting( 'SMTP_USER','webmaster@apogeegate.com' );
			$AI->set_setting( 'CRLF', "\r\n" );

			// $AI->set_setting( 'MAIL_SEND_METHOD', "smtp" );
			// $AI->set_setting( 'SMTP_AUTH', true );
			// $AI->set_setting( 'SMTP_BLURP', "1g65Rw9" );
			
			
			//PATHS  FS: file system directory structure
			$AI->set_setting( 'FS_MAIN_UPLOAD_PATH', 'uploads/' ); //general, names are assigned
			$AI->set_setting( 'FS_CONTENT_UPLOAD_PATH', 'uploads/content/' ); //admin only (original filename is kept)
			$AI->set_setting( 'FS_IMAGE_UPLOAD_PATH', 'uploads/images/' ); //images, names are assigned
			$AI->set_setting( 'FS_THUMB_UPLOAD_PATH', 'uploads/thumbs/' ); //thumbs, names are assigned
			$AI->set_setting( 'FS_FILE_UPLOAD_PATH', 'uploads/files/' );
			$AI->set_setting( 'FS_SKIN_PATH', 'system/themes/' );
			$AI->set_setting( 'FS_SESSION_SAVE_PATH', 'tmp' );
			
			//PATHS  WS:  web server directory structure
			$AI->set_setting( 'WS_CONTENT_UPLOAD_PATH', 'uploads/content/' );
			$AI->set_setting( 'WS_IMAGE_UPLOAD_PATH', 'uploads/images/' );
			$AI->set_setting( 'WS_THUMB_UPLOAD_PATH', 'uploads/thumbs/' );
			$AI->set_setting( 'WS_FILE_UPLOAD_PATH', 'uploads/files/' );
			
			$AI->set_setting( 'MAX_FILE_SIZE_UPLOAD', '10000000' );
		}break;
		
		case 'local':
			require_once( $local_settings_file );
			break;
			
		default:
		{
			die( 'SITE_CONFIG_MODE = [' . AI_SITE_CONFIG_MODE . '] (in includes/config/config.php), Invalid Configuration Argument'."<br>\n" );
		}
		break;
		
	}

	// validate that none of the defaults are used.. do not allow the system to be run unless these are not the default
	if(    $AI->get_setting('SITE_BLURB_KEY') == '[['.'SITE_BLURB_KEY]]' 
	    || $AI->get_setting('DB_USERNAME') == '[['.'DB_USERNAME]]'
	    || $AI->get_setting('DB_BLURP') == '[['.'DB_PASSWORD]]'
	    || $AI->get_setting('DEFAULT_HTTP_URL') == '[['.'DEFAULT_HTTP_URL]]'
	    || $AI->get_setting('DEFAULT_HTTPS_URL') == '[['.'DEFAULT_HTTPS_URL]]' ) {
	    	die ('Default configuration detected.  Please configure this system.');
	}
?>
