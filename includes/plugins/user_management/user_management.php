<?php
	//Copyright (c)2006 All Rights Reserved - Apogee Design Inc. 
	//Generated: 2006-11-11 02:14:32
	//DB Table: users, Unique ID: user_management, PK Field: userID
	
	require_once( ai_cascadepath( dirname(__FILE__) . '/includes/class.te_user_management.php' ) );	

	global $AI;
	
	//trigger account_type recache
	if(isset($_GET['update_perm_groups']) && $AI->perm->is_user_in_group('Website Developers')) {
		$rsm = db_query("SELECT * FROM users WHERE userID>0");
		echo "<textarea style='width:800px; height:400px;'>";
		while($rsm && ($u=db_fetch_assoc($rsm))!==false) {
			echo "\n".$u['userID']." => ".$u['account_type'];
			$AI->perm->update_user_perm_groups($u['userID'],$u['account_type']);
		}
		echo "</textarea>";
		return;
    }

    $AI->skin->js('includes/plugins/user_management/includes/usermanager.js');
	
	$dbwhere = "access_level <= " . $AI->user->access_level;
	$webdev = ($AI->user->account_type=='Website Developer');
	$admin = ($webdev || $AI->user->account_type=='Administrator');
	if(!$webdev) $dbwhere .= " AND account_type!='Website Developer'";
	if(!$admin) $dbwhere .= " AND account_type!='Administrator'";
	
	$te_user_management = new C_te_user_management( $dbwhere );
	$te_user_management->run_TableEdit();	
?>