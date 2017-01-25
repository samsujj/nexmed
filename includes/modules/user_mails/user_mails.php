<?php
	//Copyright (c)2006 All Rights Reserved - Apogee Design Inc. 
	//Generated: 2012-05-22 15:24:18 by jon
	//DB Table: share_links, Unique ID: share_links, PK Field: id
	
	require_once( ai_cascadepath( dirname(__FILE__) . '/includes/class.te_user_mails.php' ) );

	global $AI;



	$dbWhere = '';
	if($AI->user->account_type == 'Website Developer' || $AI->user->account_type == 'Administrator'){
		$dbWhere = '';
	}else{
		$dbWhere = 'userID ='.$AI->user->userID;
	}

	$te_user_mails = new C_te_user_mails($dbWhere);

	$te_user_mails->_obFieldDefault = 'email';
	$te_user_mails->_obDirDefault = 'ASC';
	$te_user_mails->set_session( 'te_obField', $te_user_mails->_obFieldDefault );
	$te_user_mails->set_session( 'te_obDir', $te_user_mails->_obDirDefault );
	$te_user_mails->_obField = $te_user_mails->get_session( 'te_obField' );
	$te_user_mails->_obDir = $te_user_mails->get_session( 'te_obDir' );

	$te_user_mails->select($te_user_mails->te_key);

	//$te_user_mails->run_TableEdit();

	$isAddimapmail = 0;
	if(isset($_GET['te_mode']) && $_GET['te_mode']== 'addimapmail'){
		$isAddimapmail = 1;
	}

	if($te_user_mails->te_mode == 'cngpass'){
		$te_user_mails->te_mode_cngpass();
	}elseif($te_user_mails->te_mode == 'addmail'){
		$te_user_mails->te_mode_addmail();
	}elseif($te_user_mails->te_mode == 'addimapmail' || $isAddimapmail){
		$te_user_mails->te_mode_addimapmail();
	}else{
		$te_user_mails->run_TableEdit();
	}


?>
