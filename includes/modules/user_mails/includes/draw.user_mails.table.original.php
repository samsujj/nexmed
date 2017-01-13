<?php
	//Copyright (c)2006 All Rights Reserved - Apogee Design Inc.
	//Generated: 2012-05-22 15:24:18 by jon
	//DB Table: share_links, Unique ID: share_links, PK Field: id
	require_once( ai_cascadepath('includes/plugins/ajax/ajax.require_once.php') );
	
?>
<script language="javascript" type="text/javascript">
<!--
	function share_links_delete_selected()
	{
		var frm = document.getElementById('share_links_table_form');

		if( confirm('Delete Selected Items?') )
		{
			frm.action = '<?php echo $this->url( 'te_mode=multidelete' ); ?>';
			frm.submit();
		}
	}

	function share_links_process_multiselect_checkbox( selectall_checkbox )
	{
		var frm = document.getElementById('share_links_table_form');

		for( var i=0; i < frm.elements.length; i++ )
		{
			if( frm.elements[i].type == 'checkbox' && frm.elements[i].name == 'te_multiselect[]' )
			{
				frm.elements[i].checked = selectall_checkbox.checked;
			}

		}
	}

	function share_links_update_sort_index(table, row)
	{
		// Fix Zebra Stripping
		$("table.te_main_table tr:even").removeClass("te_odd_row").addClass("te_even_row");
		$("table.te_main_table tr:odd").removeClass("te_even_row").addClass("te_odd_row");

		var post_str = $(table).tableDnDSerialize();

		$('#saving').css('display', 'inline');

		// Create a post request
		ajax_post_request('<?= $this->ajax_url('update_sort_index', '') ?>', post_str, ajax_handler_default);
	}
//-->
</script>

<div class="te_table share_links_table">
	<fieldset class="te">
		<legend class="te">
			<?php echo htmlspecialchars( $this->_tableTitle ); ?>
		</legend>

		<form id="share_links_table_form" class="te_table_form" name="share_links_table_form" method="post" action="<?php echo htmlspecialchars($this->url( '' )); ?>">
			<table class="te_main_table share_links_main_table" id="share_links_main_table">
				<thead>
					<tr class="te_fieldname_row">
						<?php
							if( $this->_table_controls_side != 'right' ){ echo '<td class="te_button_cell">'; $this->draw_table_contol_buttons(); echo '</td>'; }

							//DRAW LABEL CELLS ( SORT ORDER )
							$this->draw_FieldName('id', 'Id');
							$this->draw_FieldName('name', 'Name');
							$this->draw_FieldName('description', 'Description');
							$this->draw_FieldName('url', 'Url');
							$this->draw_FieldName('img_url', 'Img Url');
							

							if( $this->_table_controls_side == 'right' ){ echo '<td class="te_button_cell">'; $this->draw_table_contol_buttons(); echo '</td>'; }
						?>
					</tr>
				</thead>
				<tbody>
					<?php

						//DRAW DATA ROWS
						$table_row = db_fetch_assoc($table_result);
						for($table_i=0; $table_i < $this->_pgSize && $table_row; $table_i++)
						{
							foreach( $table_row as $n => $v )
							{
								$this->db[$n] = db_out($v);
							}

							echo "\n".'<tr class="te_data_row ' . ( $table_i % 2 == 1 ? 'te_even_row' : 'te_odd_row' ) . '" id="'.htmlspecialchars($this->db[ $this->_keyFieldName ]).'">';

							//DRAW BUTTON COLUMN - FOR THIS ROW
							if( $this->_table_controls_side != 'right' ){ echo "\n".'<td class="te_button_cell">'; $this->draw_row_control_buttons(); echo '</td>'; }

							//DRAW DATA COLUMNS - FOR THIS ROW
							echo "\n".'<!-- Id --> <td class="te_data_cell ' . ( $table_i % 2 == 1 ? 'te_even_cell' : 'te_odd_cell' ) . '" id="value_field_container_' . $this->db[ $this->_keyFieldName ] . '_id">'; $this->draw_value_field( 'id', $this->db['id'].'', $this->db[ $this->_keyFieldName ], 'table' ); echo '</td>';
							echo "\n".'<!-- Name --> <td class="te_data_cell ' . ( $table_i % 2 == 1 ? 'te_even_cell' : 'te_odd_cell' ) . '" id="value_field_container_' . $this->db[ $this->_keyFieldName ] . '_name">'; $this->draw_value_field( 'name', $this->db['name'].'', $this->db[ $this->_keyFieldName ], 'table' ); echo '</td>';
							echo "\n".'<!-- Description --> <td class="te_data_cell ' . ( $table_i % 2 == 1 ? 'te_even_cell' : 'te_odd_cell' ) . '" id="value_field_container_' . $this->db[ $this->_keyFieldName ] . '_description">'; $this->draw_value_field( 'description', $this->db['description'].'', $this->db[ $this->_keyFieldName ], 'table' ); echo '</td>';
							echo "\n".'<!-- Url --> <td class="te_data_cell ' . ( $table_i % 2 == 1 ? 'te_even_cell' : 'te_odd_cell' ) . '" id="value_field_container_' . $this->db[ $this->_keyFieldName ] . '_url">'; $this->draw_value_field( 'url', $this->db['url'].'', $this->db[ $this->_keyFieldName ], 'table' ); echo '</td>';
							echo "\n".'<!-- Img Url --> <td class="te_data_cell ' . ( $table_i % 2 == 1 ? 'te_even_cell' : 'te_odd_cell' ) . '" id="value_field_container_' . $this->db[ $this->_keyFieldName ] . '_img_url">'; $this->draw_value_field( 'img_url', $this->db['img_url'].'', $this->db[ $this->_keyFieldName ], 'table' ); echo '</td>';
							

							if( $this->_table_controls_side == 'right' ){ echo "\n".'<td class="te_button_cell">'; $this->draw_row_control_buttons(); echo '</td>'; }

							echo '</tr>';
							$table_row = db_fetch_assoc($table_result);
						}

					?>
				</tbody>
			</table>
		</form>
		<?php
			//DRAW PAGING
			if( $this->_nRows > $this->_draw_paging_for_more_than_n_results )
			{
				echo '<div class="te_paging">';
				$this->draw_Paging();
				echo '</div>';
			}
		?>
	</fieldset>
</div>
