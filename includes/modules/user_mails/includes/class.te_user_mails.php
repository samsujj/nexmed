<?php
	//Copyright (c)2006 All Rights Reserved - Apogee Design Inc.
	//Generated: 2012-05-22 15:24:18 by jon
	//DB Table: share_links, Unique ID: share_links, PK Field: id

	require_once( ai_cascadepath( 'includes/core/classes/tableedit_base.php' ) );
require_once(ai_cascadepath('includes/plugins/pop3/api.php'));

	class C_te_user_mails extends C_tableedit_base
	{

		var $upload_dir = 'uploads/user_mails/';

		//(configure) parameters
		var $_dbTableName = 'user_mails';
		var $_keyFieldName = 'id';
		var $_numeric_key = true;
		var $unique_id = 'user_mails';
		var $_tableTitle = ''; //default in constructor
		var $draw_qsearch = false;
		var $use_te_fields_system = false; // need to remove DB & DESC array definitions. Consider defaulting all draw files to the default tableedit/ draw files
		var $draw_table_menu_not_buttons = false; //displays TE_field options, multidelete, etc under one button
		var $draw_enum_as_select = true;	//don't draw radio buttons
		var $init_backbone_js = true;
		//(configure) Draw Code
		var $view_include_file = 'includes/modules/user_mails/includes/draw.user_mails.view.php';
		var $edit_include_file = 'includes/modules/user_mails/includes/draw.user_mails.edit.php';
		var $cngpass_include_file = 'includes/modules/user_mails/includes/draw.user_mails.cngpass.php';
		var $table_include_file = 'includes/modules/user_mails/includes/draw.user_mails.table.php';
		var $qsearch_include_file = 'includes/modules/user_mails/includes/draw.user_mails.qsearch.php';
		var $asearch_include_file = 'includes/modules/user_mails/includes/draw.user_mails.asearch.php';
		var $viewnav_include_file = 'includes/modules/user_mails/includes/draw.user_mails.viewnav.php';
		var $noresults_include_file = 'includes/modules/user_mails/includes/draw.user_mails.noresults.php';
		var $ajax_include_file = 'includes/modules/user_mails/includes/handler.user_mails.ajax.php';

		//(configure) ob stands for "order by" members
		var $_obFieldDefault = ''; //default in constructor
		var $_obDirDefault = "ASC";
		var $_pgSizeDefault = 20;
		var $_te_modeDefault = 'table';
		var $_default_mode_after_save = 'table';
		var $_draw_paging_for_more_than_n_results = 2;
		var $_max_results_2_select_pg_num = 200; //0 to disable
		var $_paging_size_options = array( 5, 10, 20, 50, 100, 200 ); //empty to disable
		var $_unit_label = 'Results';
		var $_table_controls_side = 'left'; // ( left, right )

		// Drag-n-Drop jQuery Sorting
		var $sort_index_field = 'id';  // If not blank, sorting is enabled using this field as the index
		var $sort_drag_handle_class = ''; // Optional class name of an element to use as handle for sorting (rather than entire row)

		function C_te_user_mails( $param_dbWhere = '' )
		{
			$this->dbWhere = $param_dbWhere;

			//INITIALIZE DATABASE VARS
			//$this->db;

			//INITIALIZE SEARCH VARS
			//these should NOT conflict with database fields above
			$this->search_vars['example_of_a_special_search_var'] = '';

			//INITIALIZE DATABASE DESCRIPTION
			//$this->desc;

			//CALL PARENT CLASS CONSTRUCTOR ( creates permissions "$this->perm", etc... )
			parent::C_tableedit_base();

			//SPECIFY MODES ALLOWED FOR INLINE-EDITIBLE FIELDS
			//the value may be 'all', 'table', 'view', or 'none'
			$this->inline_edit_db_field['id'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );
			$this->inline_edit_db_field['userID'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );
			$this->inline_edit_db_field['email'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );
			$this->inline_edit_db_field['password'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );
			$this->inline_edit_db_field['status'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );

			//Don't inline edit the primary key
			$this->inline_edit_db_field[ $this->_keyFieldName ] = 'none';


			$this->te_permit['insert_user_mails'] = $this->perm->get('insert_user_mails');
		}

		function validate_write()
		{
			global $AI;

			//OCCURS BEFORE DATABASE INSERT OR UPDATE ( te_modes : insert, copy, update, ajax )

			//write errors occur if $this->write_error_msg != '' ( this will allow the user to modify their input )
			$this->write_error_msg = '';

			if( $this->_numeric_key || ( $this->te_mode != 'update' && $this->te_mode != 'insert' ) )
			{
				$this->writable_db_field[ $this->_keyFieldName ] = false;  //don't allow the primary key to be overwritten
			}
			else
			{
				$this->writable_db_field[ $this->_keyFieldName ] = true;
			}

			/*if($this->te_mode == 'insert') {
				$this->writable_db_field['added_on'] = true;
				$this->db['added_on'] = date('Y-m-d H:i:s');
				$this->writable_db_field['user_id'] = true;
				$this->db['user_id'] = $AI->user->userID;
			}

			if($this->te_mode == 'update') {
				$this->writable_db_field['last_modified_on'] = true;
				$this->db['last_modified_on'] = date('Y-m-d H:i:s');
				$this->writable_db_field['user_id'] = true;
				$this->db['user_id'] = $AI->user->userID;
			}*/




			// Automatically generate the sorting index
			if( $this->te_mode == 'insert' || $this->te_mode == 'copy' ) {
				if(trim($this->sort_index_field) != '') {
					$this->writable_db_field[$this->sort_index_field] = true;
					$this->db[$this->sort_index_field] = floor($this->get_max_sort_index()) + 1;
				}
			}
		}

		function finalize_write()
		{

			//OCCURS AFTER SUCCESSFUL DATABASE INSERT OR UPDATE ( te_modes : insert, copy, update, ajax )


		}

		function validate_delete( $delKey )
		{
			//BEFORE DELETE -- return false to abort delete
			return true;
		}

		function finalize_delete( $delKey )
		{
			//AFTER DELETE
		}

		function calcSqlQuery_ASearch()
		{
			$asearch_sql = '';

			//ADD SEARCHES FOR DB FIELDS
			if( $this->search_vars['id'] != '' ){ $asearch_sql .= "AND this.id = '" . db_in( $this->search_vars['id'] ) . "' "; }
			if( $this->search_vars['email'] != '' ){ $asearch_sql .= "AND this.email = '" . db_in( $this->search_vars['email'] ) . "' "; }

			return $asearch_sql;
		}

		/**
		 * DRAW INPUT FIELDS
		 * $mode : asearch, edit, inline
		 * $element_id : this will default to $fieldname if left blank
		 */
		function draw_input_field( $fieldname, $value, $mode, $element_id = '' )
		{
			global $AI;
			if( $element_id == '' ){ $element_id = $fieldname; }

			switch( $fieldname )
			{
				case 'userID':
				case 'email': {
					echo '<input id="'.$fieldname.'" name="'.$fieldname.'" size="100" maxlength="255" value="'.$value.'" type="text" readonly="readonly">';
				} break;
				case 'password': {
					echo '<input id="'.$fieldname.'" name="'.$fieldname.'" value="'.base64_decode(base64_decode($value)).'" type="text" readonly="readonly">';
				} break;

				default: { $this->draw_input_field_by_desc( $fieldname, $value, $mode, $this->desc[ $fieldname ], $element_id ); } break;
			}

		}

		/**
		 * DRAW VALUE FIELDS
		 */
		function draw_value_field( $fieldname, $value, $key, $mode )
		{
			global $AI;
			//IF THEY CAN "INLINE-EDIT" THEN SET IT UP
			if( $this->perm->get('ajax') && ( $this->inline_edit_db_field[ $fieldname ] == $mode || $this->inline_edit_db_field[ $fieldname ] == 'all' ) )
			{
				echo '<div class="te_inline_edit_cell" onclick="javascript:ajax_get_request( \'' . htmlspecialchars($this->ajax_url( 'inline_edit', 'te_key=' . $key . '&fieldname=' . $fieldname . '&view_mode=' . $mode )) . '\', ajax_handler_default );" >';
			}

			//DRAW THE VALUES
			if( $mode == 'table' )
			{
				
				switch( $fieldname )
				{
					
					/*case 'user_id': { echo util_trim_string( htmlspecialchars( $value ), 25, '..' ) . '&nbsp;'; } break;
					case 'share_link_id': { echo util_trim_string( htmlspecialchars( $value ), 25, '..' ) . '&nbsp;'; } break;*/
					//case 'title': {echo '<h4>'. htmlspecialchars( $value ). '</h4>'; } break;
					case 'status': {
						if($value){
							echo 'Active';
						}else{
							echo 'Inactive';
						}
					} break;

					case 'userID': {
						$name = '';
						$data = $AI->db->GetAll("SELECT * FROM users WHERE userID = " . (int) $value);
						if(isset($data[0])){
							$name =$data[0]['first_name']." ".$data[0]['last_name'];
						}
						echo $name;

					} break;

					default: { echo  htmlspecialchars( $value ). '&nbsp;'; } break;
				}
			}
			elseif( $mode == 'view' )
			{
				//echo 888;
				switch( $fieldname )
				{
					/*case 'user_id': { echo htmlspecialchars( $value ) . '&nbsp;'; } break;
					case 'share_link_id': { echo htmlspecialchars( $value ) . '&nbsp;'; } break;
					case 'name': { echo htmlspecialchars( $value ) . '&nbsp;'; } break;
					case 'pixel_value': { echo htmlspecialchars( $value ) . '&nbsp;'; } break;*/

					default: { echo htmlspecialchars( $value ) . '&nbsp;'; } break;
				}
			}
			else
			{
				echo 'Error: Invalid view mode specified.';
			}

			//IF THEY CAN "INLINE-EDIT" THEN FINISH IT UP
			if( $this->perm->get('ajax') && ( $this->inline_edit_db_field[ $fieldname ] == $mode || $this->inline_edit_db_field[ $fieldname ] == 'all' ) )
			{
				echo '</div>';
			}
		}


		function insert_data($arr=array()){
			if(isset($arr['password'])){
				$arr['password'] = base64_encode(base64_encode($arr['password']));
			}
			db_query("INSERT INTO `user_mails` (`userID`, `email`, `password`) VALUES (".@$arr['userID'].", '".@$arr['email']."', '".@$arr['password']."');");
		}

		function get_data($userid = 0){
			global $AI;
			$data = $AI->db->GetAll("SELECT * FROM user_mails WHERE userID = " . (int) $userid);

			if(isset($data[0])){
				$password = base64_decode(base64_decode($data[0]['password']));

				return array('email'=>$data[0]['email'],'password'=>$password);
			}else{
				return array();
			}

		}

		function te_mode_cngpass(){
			global $AI;

			if( $this->te_class == $this->unique_id && $_SERVER['REQUEST_METHOD'] == 'POST' && $this->is_valid_key( $this->te_key ) )
			{

				$cpanelusr = 'nexmed';
				$cpanelpass = 'l0PS8AyMm0aB';
				$xmlapi = new xmlapi('galaxy.apogeehost.com');
				$xmlapi->set_port( 2083 );
				$xmlapi->password_auth($cpanelusr,$cpanelpass);
				$xmlapi->set_debug(0);



				$err = $AI->user->validate_password($_POST['newpassword']);

				if(empty($_POST['oldpassword'])){
					$this->write_error_msg = 'Please enter old password';
				}else if($_POST['oldpassword'] != base64_decode(base64_decode($_POST['password']))){
					$this->write_error_msg = 'Old password does not match';
				}else if(empty($_POST['newpassword'])){
					$this->write_error_msg = 'Please enter new password';
				}else if(!$err){
					$this->write_error_msg = $err;
				}else if($_POST['confpassword'] != $_POST['newpassword']){
					$this->write_error_msg = 'Confirm password does not match';
				}else{


					$arr = explode('@',$_POST['email']);

					$result = $xmlapi->api1_query($cpanelusr, 'Email', 'passwdpop', array(  $arr[0], $_POST['newpassword'],"nexmedsolutions.com"));
					if(isset($result->error)){
						$this->write_error_msg = strip_tags($result->data->result);
					}else{
						$sql_str = "UPDATE " . $this->_dbTableName . " SET password = '".base64_encode(base64_encode($_POST['newpassword']))."' WHERE " . $this->_keyFieldName . " = " . (int)db_in($this->te_key);
						$rs = db_query( $sql_str );

						$url = $this->te_redirect_url('default');
						util_redirect( $url );
					}


				}

			}
			elseif( $this->is_valid_key( $this->te_key ) )
			{
				if( !$this->select( $this->te_key ) )
				{
					$this->draw_MissingDataError();
				}
			}

			$this->get_relative_paging_info();
			$this->draw_unique_div_open();
			$this->draw_cngpass( $this->url( 'te_mode=cngpass&te_key=' . $this->te_key . '&te_row=' . (int) $this->_row_i) );
			$this->draw_unique_div_close();
		}

		function draw_cngpass($postURL)
		{
			global $AI;

			$this->include_plugin_css();
			require( ai_cascadepath( $this->cngpass_include_file ) );
			echo '<div class="te_edit_stats" data-row-i="' . (int) $this->_row_i . '"></div>';
		}




	}//~class C_te_share_links extends C_tableedit_base
?>
