<?php
	//Copyright (c)2006 All Rights Reserved - Apogee Design Inc.
	//Generated: 2012-05-22 15:24:18 by jon
	//DB Table: share_links, Unique ID: share_links, PK Field: id

	switch( $this->ajax_cmd )
	{
		case 'inline_edit':
		{
			$fieldname = trim( @$_GET['fieldname'].'' );

			$container_id = 'value_field_container_' . $this->te_key . '_' . $fieldname;
			$edit_box_id = 'edit_box_' . $container_id;

			$view_mode = trim( @$_GET['view_mode'].'' );

			if( $fieldname != '' && $this->is_valid_key( $this->te_key ) )
			{
				if( $this->select( $this->te_key ) )
				{
					echo $container_id . '|';
					echo '<div id="' .$edit_box_id . '" class="te_inline_edit_box">';
					$this->draw_input_field( $fieldname, $this->db[ $fieldname ], 'inline', 'inline_' . $this->te_key . '_' . $fieldname );
					?>
					<div class="te_inline_edit_buttons" >
					<a class="te_inline_edit_buttons te_inline_save_button" onclick="<?php echo 'javascript:ajax_post_request( \'' . $this->ajax_url( 'inline_save', 'te_key=' . $this->te_key . '&fieldname=' . $fieldname . '&view_mode=' . $view_mode ) . '\', \'' . $fieldname . '=\' + escape( ajax_get_value(\'inline_' . $this->te_key . '_' . $fieldname . '\') ), ajax_handler_default, \'\', false  )'; ?>" ><span class="te_inline_edit_buttons te_inline_save_button">Save</span></a>
					<a class="te_inline_edit_buttons te_inline_cancel_button" onclick="javascript:document.getElementById('<?php echo $edit_box_id; ?>').style.display='none';" ><span class="te_inline_edit_buttons te_inline_cancel_button">Cancel</span></a>
					</div>
					<?php
					echo '</div>';
					$this->draw_value_field( $fieldname, $this->db[ $fieldname ].'', $this->te_key, $view_mode );
				}
				else
				{
					echo 'ajax_error|Error: The data item was not found.';
				}
			}
			elseif( !$this->is_valid_key( $this->te_key ) )
			{
				echo 'ajax_error|Error: No valid key specified.';
			}
			else
			{
				echo 'ajax_error|Error: Inline edit field name not specified.';
			}
		}break;

		case 'inline_save':
		{
			$fieldname = trim( @$_GET['fieldname'].'' );
			$value = trim( ai_magic_quotes() ? stripslashes( @$_POST[ $fieldname ].'' ) : @$_POST[ $fieldname ].'' );
			$view_mode = trim( @$_GET['view_mode'].'' );

			$container_id = 'value_field_container_' . $this->te_key . '_' . $fieldname;

			if( $fieldname != '' && $this->is_valid_key( $this->te_key ) )
			{
				$this->set_all( $this->writable_db_field, false );
				$this->writable_db_field[ $fieldname ] = true;
				$this->db[ $fieldname ] = $value;

				$this->update( $this->te_key );

				if( $this->write_error_msg != '' )
				{
					echo 'ajax_error|' . $this->write_error_msg;
				}
				else
				{
					//REFRESH THE DATA AND REDRAW THE FIELD BACK TO THE BROWSER
					$this->select( $this->te_key );
					echo $container_id . '|';
					$this->draw_value_field( $fieldname, $this->db[ $fieldname ].'', $this->te_key, $view_mode );
				}
			}
			elseif( !$this->is_valid_key( $this->te_key ) )
			{
				echo 'ajax_error|Error: No valid key specified.';
			}
			else
			{
				echo 'ajax_error|Error: Inline save field name not specified.';
			}
		}break;

		case 'update_sort_index': // ~ JosephL<JosephL@apogeeinvent.com> 2008.10.29
			global $AI;
			$err_str = '';
			$new_order = array();
			$original_order = array();
			$to_save_order = array();

			if($this->sort_index_field == '') {
				echo 'ajax_error|No Sort Index Field Specified';
				break;
			}

			if(!isset($_POST[$this->unique_id.'_main_table'])) {
				echo 'ajax_error|Sort Order Index Array Not Specified';
				break;
			}

			// Build new order array
			foreach ($_POST[$this->unique_id.'_main_table'] as $order_num => $te_key) {
				if(trim($te_key) != '') {
					$new_order[$te_key] = (int)$order_num;
				}
			}

			$this->get_orderby_info();
			if($this->_obDir == "DESC") {
				// These results are in the reverse order of how we want them
				$new_order = array_reverse($new_order, true);
			}


			// Build Original Order Array
			$sql = 'SELECT '.$this->_keyFieldName.', '.$this->sort_index_field.' FROM '.$this->_dbTableName.' WHERE '.$this->_keyFieldName.' IN ('.implode(',', array_keys($new_order)).')';
			$new_order_tmp = $AI->db->GetAll($sql, $this->_keyFieldName);
			//print_r($new_order_tmp);
			//die("ajax_error|$sql".'==='.print_r($_POST[$this->unique_id.'_main_table'],true));
			foreach ($new_order_tmp as $id => $row) {
				$original_order[$id] = (double)$row[$this->sort_index_field];
			}

			$order_num = '';
			foreach ($new_order as $id => $new_o_place) {
				if($order_num === '') {
					// This is the first in the new order, so set the value to compare to the next
					$order_num = $original_order[$id];
				}
				else {
					// The original order is less than the new order, increase
					if($order_num >= $original_order[$id]) {
						$order_num += .01;
						$to_save_order[$id] = $order_num;
					} else {
						$order_num = $original_order[$id];
					}
				}
			}

			// Update any rows that need updating determined above
			foreach ($to_save_order as $id => $new_order) {
				$sql = 'UPDATE '.db_in($this->_dbTableName).' SET '.db_in($this->sort_index_field).' = '.db_in($new_order).' WHERE '.db_in($this->_keyFieldName).' = '.db_in($id);
				if(!db_query($sql)) {
					$err_str .= 'Could not Update Row <!-- '.$sql.' ['.db_error().'] --><br>';
				}
			}

			// Fade out the saving and display any db errors (if any)
			echo 'ajax_run_script|$("#saving").fadeOut("Slow");';
			if($err_str != '') {
				echo '$("#ajax_error").html("An error occurred:<br>'.addslashes($err_str).'");';
			}
			break;

		case "check_url":
		{
			global $AI;
			
			echo 'ajax_run_script|$("#url_sub_1").removeClass("loading");';
	
			$domain_id = util_GET('domain_id', 0);
			$sub_domain_id = util_GET('sub_domain_id', 0);
			$url = (isset($_GET['url']) ? urldecode($_GET['url']) : '');
			$id = util_GET('id', 0);
			
			$count = (int) db_lookup_scalar("SELECT count(*) FROM share_links WHERE id <> " . (int) db_in($id) . " AND url = '" . db_in($url) . "' AND domain_id = " . (int) db_in($domain_id) . " AND sub_domain_id = " . (int) db_in($sub_domain_id) . "; ");
			
			if(isset($count)) {
				switch($count) {
					case 0:
					case "":
					{
						echo '$("#url_sub_1").css({"border":"2px solid green"});';
						echo '$(".button_next .icon_button").show(125, function() {';
						echo 'update_height();' . "\n";
						echo 'goto_page(2);' . "\n";
						echo '});';

					} break;
					default:
					{
						echo '$("#url_sub_1").css({"border":"2px solid red"});';
					} break;
				}
			} else {
				echo '$("#url_sub_1").css({"border":"2px solid red"});';
			}
		} break;

		default:
			echo 'ajax_error|Error: Unknown Ajax Command.';
			break;
	}

?>