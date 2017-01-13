<?php
	/**
	 * AI CORE PLATFORM - CASCADING CODEBASE SYSTEM
	 * (c)Copyright 2010 ApogeeINVENT, All Rights Reserved
	 * j0zf & DrewL - 2010.5.2
	 * Added support for an alternate local version of setting.  If present then system will use that version.  For development versions of the same SVN controlled system.
	 * Alternative file is not SVN controlled. ~JosephL 201.03.01
	 * Added Central Core-switching Check ~DrewL 2012.10.02	 
	 */
	
	global $AI_CODEBASE_CASCADE; // array of code-bases starting at originator and ending with the core
	global $AI_CODEBASE_CASCADE_FLIPPED;

	//PARAMATER : SPECIFY THE PARENT CODE-BASE IN: config/parent_codebase_path.txt
	// Check to see if a local version exists.  This is a non SVN controlled version of the file used specifically in development installed lcoations.
	$parent_codebase_path_file = dirname(__FILE__).'/config/parent_codebase_path.local.txt';
	if(!file_exists($parent_codebase_path_file)) $parent_codebase_path_file = dirname(__FILE__).'/config/parent_codebase_path.txt';
	if(!file_exists($parent_codebase_path_file)) { die('Parent_codebase config file is required: '.dirname(dirname(__FILE__))); }
	$PARENT_CODEBASE = trim(file_get_contents($parent_codebase_path_file).''); // if empty then this is the root codebase
	
	//CALL 'CENTRAL PRE-INIT' (CHECKS FOR OVERRIDE COOKIES, ETC)
	//ONLY CALL THE CENTRAL INIT IF THIS IS THE FIRST INIT.PHP FILE TO BE HIT
	$central_pre_init = '/home/aicore/public_html/backbone/includes/init_central.php'; 
	if( (!isset($AI_CODEBASE_CASCADE) || !is_array($AI_CODEBASE_CASCADE)) && file_exists($central_pre_init)) {
		$base_path = dirname(dirname(__FILE__)); //for conditionals in central_init that need to identify this project/path
		require_once($central_pre_init);
	}
	
	//BUILD THE AI_CODEBASE_CASCADE PATH ARRAY AND CALL AICORE
	$AI_CODEBASE_CASCADE[] = dirname(dirname(__FILE__)); // add this path to the codebase-cascade
	
	if($PARENT_CODEBASE != '') {
		// IF THERE IS A PARENT CODE-BASE THEN RUN IT'S INIT FILE
		$PARENT_CODEBASE = rtrim(trim($PARENT_CODEBASE),'/');
		require_once( $PARENT_CODEBASE . '/includes/init.php' );
	} else {
		// WE'RE NOW AT THE CORE/TOP-MOST CODEBASE
		// ZIP THROUGH THE LOWER CODE-BASES ATTEMPTING TO FIND THE AI-CORE
		$AI_CODEBASE_CASCADE_FLIPPED = array_flip($AI_CODEBASE_CASCADE);
		require_once( dirname(__FILE__).'/core/functions/cascade.php' );
		require_once(ai_cascadepath('includes/core/main/main.php'));
	}
?>