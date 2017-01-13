<?php
	//Copyright (c)2006 All Rights Reserved - Apogee Design Inc. 
	//Generated: 2012-05-22 15:24:18 by jon
	//DB Table: share_links, Unique ID: share_links, PK Field: id
	
	require_once( ai_cascadepath( dirname(__FILE__) . '/includes/class.te_repmanager.php' ) );

/*echo '<link href="includes/modules/video_manager/video_manager.css" rel="stylesheet">';
echo '<link href="includes/modules/video_manager/bootstrap.min.css" rel="stylesheet">';*/

	global $AI;





	$te_repmanager = new C_te_video_manager();

	$te_repmanager->_obFieldDefault = 'time';
	$te_repmanager->_obDirDefault = 'DESC';
	$te_repmanager->set_session( 'te_obField', $te_repmanager->_obFieldDefault );
	$te_repmanager->set_session( 'te_obDir', $te_repmanager->_obDirDefault );
	$te_repmanager->_obField = $te_repmanager->get_session( 'te_obField' );
	$te_repmanager->_obDir = $te_repmanager->get_session( 'te_obDir' );

	$te_repmanager->select($te_repmanager->te_key);

	$te_repmanager->run_TableEdit();
?>
