<?php
	//Copyright (c)2006 All Rights Reserved - Apogee Design Inc.
	//Generated: 2012-05-22 15:24:18 by jon
	//DB Table: share_links, Unique ID: share_links, PK Field: id

	require_once( ai_cascadepath( 'includes/core/classes/tableedit_base.php' ) );

global $AI;



	class C_te_video_manager extends C_tableedit_base
	{

		//var $upload_dir = 'uploads/video_manager/';

		//(configure) parameters
		var $_dbTableName = 'video_manager';
		var $_keyFieldName = 'id';
		var $_numeric_key = true;
		var $unique_id = 'video_manager';
		var $_tableTitle = ''; //default in constructor
		var $draw_qsearch = false;
		var $use_te_fields_system = false; // need to remove DB & DESC array definitions. Consider defaulting all draw files to the default tableedit/ draw files
		var $draw_table_menu_not_buttons = false; //displays TE_field options, multidelete, etc under one button
		var $draw_enum_as_select = true;	//don't draw radio buttons
		var $init_backbone_js = true;
		//(configure) Draw Code
		var $view_include_file = 'includes/modules/video_manager/includes/draw.video_manager.view.php';
		var $edit_include_file = 'includes/modules/video_manager/includes/draw.video_manager.edit.php';
		var $table_include_file = 'includes/modules/video_manager/includes/draw.video_manager.table.php';
		var $qsearch_include_file = 'includes/modules/video_manager/includes/draw.video_manager.qsearch.php';
		var $asearch_include_file = 'includes/modules/video_manager/includes/draw.video_manager.asearch.php';
		var $viewnav_include_file = 'includes/modules/video_manager/includes/draw.video_manager.viewnav.php';
		var $noresults_include_file = 'includes/modules/video_manager/includes/draw.video_manager.noresults.php';
		var $ajax_include_file = 'includes/modules/video_manager/includes/handler.video_manager.ajax.php';

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

		function C_te_video_manager( $param_dbWhere = '' )
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
			$this->inline_edit_db_field['title'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );
			$this->inline_edit_db_field['description'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );
			$this->inline_edit_db_field['type'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );
			$this->inline_edit_db_field['file'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );
			$this->inline_edit_db_field['status'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );
			$this->inline_edit_db_field['priority'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );
			$this->inline_edit_db_field['time'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );
			$this->inline_edit_db_field['live_commentry	'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );
			$this->inline_edit_db_field['images	'] = 'none';//( $this->perm->get('ajax_cmd_inline_edit') ? 'all' : 'none' );

			//Don't inline edit the primary key
			$this->inline_edit_db_field[ $this->_keyFieldName ] = 'none';


			$this->te_permit['insert_video_manager'] = $this->perm->get('insert_video_manager');
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


			require_once( ai_cascadepath('includes/core/classes/file.php') );
			$f = new C_file();

			$rand = rand()."_".time();
			$filetypes = array('mp4');


				if($_POST['type'] == 1){

					if( isset( $_FILES['file_upload'] ) && $f->receive('file_upload') )
					{
						$ext = pathinfo($_FILES["file_upload"]["name"], PATHINFO_EXTENSION);

						if(in_array(strtolower($ext),$filetypes)) {

							$fname = util_cleanup_string( $f->name, '_', 'abcedfghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.-' );

							$fname = $rand."_".$fname;
							// chmod( $this->upload_dir . $fname, 0644 );

							//echo $fname;exit		;
							if($f->make_dir( $this->upload_dir ) && $f->save(  $this->upload_dir.$fname )) {
								$AI->db->Update('video_manager', array( 'file' => $fname ), "id=" . (int)db_in( $this->te_key ) );
								$this->db['file'] = $fname;
							}
							else {
								$this->write_error_msg = "File not saved. " . $f->errorText;
							}
						}else{
							$this->write_error_msg = "File type not allowed. Upload only mp4 type video.";
						}
					}else if(empty($_POST['file'])){
						$this->write_error_msg = "Please upload video";
					}

				}else{
					if(empty($_POST['file'])){
						$this->write_error_msg = "Please select youtube video";
					}
				}



			


			/*if( isset( $_FILES['file_upload'] ) && $f->receive('file_upload') )
			{

				$fname = util_cleanup_string( $f->name, '_', 'abcedfghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.-' );

				$fname = $rand."_".$fname;
				// chmod( $this->upload_dir . $fname, 0644 );

				//echo $fname;exit		;
				  if($f->make_dir( $this->upload_dir ) && $f->save(  $this->upload_dir.$fname )) {
					$AI->db->Update('video_manager', array( 'url' => $fname ), "id=" . (int)db_in( $this->te_key ) );
				}
				else {
					$this->write_error_msg = "File not saved. " . $f->errorText;
				}
			}*/

			/*if( isset( $_FILES['file_upload']['name'] ) && trim( $_FILES['file_upload']['name'] ) != '' )
			{
				$fname = util_cleanup_string( stripslashes($_FILES['file_upload']['name']), '_', 'abcedfghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.' );
				$fname = $rand."_".$fname;
				if( file_exists( $this->upload_dir.$fname ) )
				{
					$this->db['url'] = $this->upload_dir . $fname;
					//$this->write_error_msg = 'That filename already exists, please rename your file before uploading.';
				}
			}*/



			// Automatically generate the sorting index
			if( $this->te_mode == 'insert' || $this->te_mode == 'copy' ) {
				$this->writable_db_field['time'] = true;
				$this->db['time'] = time();
				if(trim($this->sort_index_field) != '') {
					$this->writable_db_field[$this->sort_index_field] = true;
					$this->db[$this->sort_index_field] = floor($this->get_max_sort_index()) + 1;
				}
			}
		}

		function finalize_write()
		{

			//OCCURS AFTER SUCCESSFUL DATABASE INSERT OR UPDATE ( te_modes : insert, copy, update, ajax )


		//	util_redirect('video_manager?te_share_link_id='.$this->db['share_link_id']);

		}

		function validate_delete( $delKey )
		{
			//BEFORE DELETE -- return false to abort delete
			return true;
		}

		function finalize_delete( $delKey )
		{


			//util_redirect('video_manager?te_share_link_id='.$this->db['share_link_id']);
		}

		function calcSqlQuery_ASearch()
		{
			$asearch_sql = '';
			//echo $asearch_sql;
			//ADD SEARCHES FOR DB FIELDS
			//if( $this->search_vars['id'] != '' ){ $asearch_sql .= "AND this.id = '" . db_in( $this->search_vars['id'] ) . "' "; }
//			if( $this->search_vars['title'] != '' ){ $asearch_sql .= "AND this.title = '" . db_in( $this->search_vars['title'] ) . "' "; }
			if( $this->search_vars['title'] != '' ){ $asearch_sql .= "AND this.title like '%" . db_in( $this->search_vars['title'] ) . "%' "; }
			if( $this->search_vars['type'] != '' ){ $asearch_sql .= "AND this.type = '" . db_in( $this->search_vars['type'] ) . "' "; }

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
				//DRAW THE INPUT FIELD BASED UPON THE DATABASE'S DESCRIBE RESULTS

				case 'file': {
					echo '<input type="hidden" name="url" id="url" value="' . htmlspecialchars( $value ) . '">';
					if( $value != '' )
					{
						//echo 'Keep this file: ' . htmlspecialchars($value) . '?<br>Or select new file: ';
					}
					echo '<input type="hidden" name="file" id="file" value="' . htmlspecialchars( $value ) . '">';
					echo '<input type="file" name="file_upload" id="file_upload" value="">';

					echo '<input type="text" placeholder="search youtube" name="file_upload" id="youtubevalue">
					<!--<button id="search-button"    onclick="keyWordsearch()">Search</button>  -->  
					<input type="button" style="float:left;" class="search_button" id="search-button" onclick="keyWordsearch()" value="Search">
					<div class="clear">&nbsp;</div>
					 <div id="myModal" class="modal fade bs-example-modal-md searchmodel" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Select video</h4>
                </div>
                <div class="modal-body" id="results">
                    
                </div>
                <!--<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addVideo()">Add video</button>
                </div>-->
            </div>
        </div>
    </div>';

					echo '<div id="filedetails"></div>';

					//echo '<p style="font-weight:bold">The images should be a square aspect ratio and will be shrunk to 250x250</p>';
				} break;

				case 'type' : {

					echo '<select id="'.$fieldname.'"  name="'.$fieldname.'"  onchange="gettype(this)"><option value="0" '.(($value == 0)?'selected="selected"':'').'>Youtube Video</option><option value="1" '.(($value == 1)?'selected="selected"':'').'>MP4 Video</option></select>';
				}break;

				case 'live_commentry' : {

					print_r($this->db);

					echo '<script src="http://cdn.tinymce.com/4/tinymce.min.js"></script>';
					//echo '<script src="includes/plugins/tinymce/tinymce.min.js"></script>';
					echo '<script>


$(function(){
    tinymce.init({
  selector: \'textarea#live_commentry\',
  height: 500,
  menubar: false,
  plugins: [
    \'advlist autolink lists link image charmap print preview hr anchor pagebreak\',
    \'searchreplace wordcount visualblocks visualchars code fullscreen\',
    \'insertdatetime media nonbreaking save table contextmenu directionality\',
    \'emoticons template paste textcolor colorpicker textpattern imagetools code toc\'
  ],
  toolbar: \'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor emoticons image | code\'  
});
})

</script>';
				/*	echo '<script src="includes/plugins/ckeditor/ckeditor.js"></script>';
                    echo '<script type="text/javascript">
	window.onload = function()
	{
		CKEDITOR.replace( "live_commentry", {
            toolbar : "Full",
    });
	};
</script>';*/

					$this->draw_input_field_by_desc( $fieldname, $value, $mode, $this->desc[ $fieldname ], $element_id );

				}break;

				case 'images' : {
					echo '<input type="text" name="images" id="images" value="' . htmlspecialchars( $value ) . '">';
				}

				default: { $this->draw_input_field_by_desc( $fieldname, $value, $mode, $this->desc[ $fieldname ], $element_id ); } break;
			}

		}

		/**
		 * DRAW VALUE FIELDS
		 */
		function draw_value_field( $fieldname, $value, $key, $mode )
		{
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
					case 'name': {echo '<h4>'. htmlspecialchars( $value ). '</h4>'; } break;
					case 'url': {
						echo (file_exists($value))?'<img src="'. $value .'" style="max-width: 150px; max-height: 150px;" />':'';
					} break;
					case 'status': {
						if($value){
							echo 'Active';
						}else{
							echo 'Inactive';
						}
					} break;
					case 'type': {
						if($value){
							echo 'MP4 video ';
						}else{
							echo 'Youtube video';
						}
					} break;
					case 'file': {
							echo '<img onclick="openvideo5(\''.$value.'\')" style="width:100px;height:100px;" src="https://i.ytimg.com/vi/'.$value.'/hqdefault.jpg" alt="#" >';

					} break;

					case 'time': {
						echo date("jS M, Y",$value);

					} break;

					default: { echo util_trim_string( htmlspecialchars( $value ), 25, '..' ) . '&nbsp;'; } break;
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


		function draw_image_list($id=0,$type=""){
			global $AI;

			if (util_rep_id() == 0) {
				$rep_name = "top";
			}elsE{
				$rep_data = db_lookup_assoc("SELECT username FROM users WHERE userID = " . (int) util_rep_id() . " LIMIT 1;");
				if(count($rep_data)>0) {
					$rep_name = $rep_data['username'];
				}else{
					$rep_name = "top";
				}
			}

			if($type == 2){
				$s_type = 'Display Ads';
			}else if($type == 3){
				$s_type = 'Mobile Ads';
			}else{
				$s_type = 'Banner Ads';
			}

			$share_arr = $AI->db->getAll("SELECT * FROM `share_links` WHERE id = " . (int) db_in($id));

			$share_link_url = $share_arr[0]['url'];

			$share_link_url = str_replace('[sub_domain]', $rep_name, $share_link_url);

			$share_link_url = nl2br(h(util_tracking_url($share_link_url, util_cleanup_keystr('share_link_'.$share_arr[0]['name']), $AI->user->userID, null, util_pretty('share_link_'.$share_arr[0]['name']))));

			

			$pixel_arr = $AI->db->getAll("SELECT * FROM `video_manager` WHERE share_link_id = " . (int) db_in($id) . " AND status=1 AND type='".$s_type."';");

			echo '<div style="padding:25px 0; background:#fff; width:100%">
<h2 style="width: 100%; text-align: center; margin:0; padding: 5px 0px; font-size: 26px; color: #333;" >Banners</h2>
<div style="width: 180px; height:2px; background: #333; margin:0 auto;"></div>
</div>';

			if(count($pixel_arr)){




				echo '<div style="width: 100%; margin: 0px auto; margin-bottom: 30px; border-bottom: none; background: #fff; ">';

				foreach($pixel_arr as $p) {

					$img_url = (file_exists($p['url']))?'<img src="http://www.epiclyfe.com/image?imgurl='.$p['url'].'&w='.$p['width'].'&h='.$p['height'].'&ar=1&e=0&cr=0" style="border: solid 1px #ccc; display:block; margin:0 auto; max-width:100%" />':'';

					echo '<div style="width: 96%; padding:20px  2% 5px; border-bottom: solid 1px #ccc;">';
					echo $img_url;
					echo '<textarea style="width:90%; box-shadow: none; display: block;  min-height: 20px; border: solid 1px #ccc; resize: none; margin: 5px auto; max-width: 430px; height: 110px;"><a href="'.$share_link_url.'" target="_blank"><img src="http://www.epiclyfe.com/image?imgurl='.$p['url'].'&w='.$p['width'].'&h='.$p['height'].'&ar=1&e=0&cr=0" alt="'.$p['name'].'" border="0" /></a></textarea>';
					//echo '<button class="info clip_button" style="height:30px; padding-top: 5px; display: block; margin: 0 auto;" href_txt="cbv cvb">Grab Copy</button>';
					echo '</div>';


				}


			}else{
				echo '<div style="width: 80%; margin: 0px auto; padding: 40px 0; text-align: center; font-size: 24px; border: solid 1px #ccc; color:#ff0000;    background: #fff; ">No Banner Found</div>';
			}

			echo "</div>";
		}


		function draw_file_table_list($value='',$type=0,$title=''){
			if(!empty($value)){
				if($type){
					echo '<div style="position: relative;"><div style="position: absolute; top:20%; left: 32%; width:24px; height: 24px;"><img onclick="openvideo51(\''.$value.'\',\''.$title.'\')" src="includes/modules/video_manager/images/videoplay.png"></div><div class="videowrap" onclick="openvideo51(\''.$value.'\',\''.$title.'\')"></div>';
				}else{
					echo '<div style="position: relative;"><div style="position: absolute; top:20%; left: 32%;width:24px; height: 24px;"><img onclick="openvideo5(\''.$value.'\',\''.$title.'\')" src="includes/modules/video_manager/images/videoplay.png"></div><img onclick="openvideo5(\''.$value.'\',\''.$title.'\')" style="width:100px; cursor:pointer;" src="https://i.ytimg.com/vi/'.$value.'/hqdefault.jpg" alt="#" ></div>';
				}
			}

		}


	}//~class C_te_share_links extends C_tableedit_base
?>
