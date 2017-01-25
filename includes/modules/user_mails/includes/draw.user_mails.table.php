<?php

global $AI;

// Merge Codes
$sub_domain = $AI->user->username;

?>

<!--<link href="includes/modules/share_asset/share_asset.css" rel="stylesheet">-->

<script language="javascript" type="text/javascript">
<!--
	function share_links_update_sort_index(table, row)
	{
		// Fix Zebra Stripping
		//$("table.te_main_table tr:even").removeClass("te_odd_row").addClass("te_even_row");
		//$("table.te_main_table tr:odd").removeClass("te_even_row").addClass("te_odd_row");

		var post_str = $(table).tableDnDSerialize();
		//$('#saving').css('display', 'inline');

		// Create a post request
		ajax_post_request('<?= $this->ajax_url('update_sort_index', '') ?>', post_str, ajax_handler_default);
	}
-->
</script>

<?php

	//echo '<button onclick="document.location = \'' . h($this->url('te_mode=insert')) . '\'; return false;">New</button>';


echo "<h2>User Mails Manager</h2>";
echo '<p>&nbsp;</p><!--spacer-->';

echo '<table class="te_main_table pixel_main_table" id="pixel_main_table">';


echo "<tr>";
echo "<th>User</th>";
echo "<th>Email</th>";
echo "<th>Action</th>";
echo "</tr>";


//var_dump($table_result);

$table_row = db_fetch_assoc($table_result);

for ( $table_i = 0; $table_i < $this->_pgSize && $table_row; $table_i++ )
{


	if (true) {

		$ai_sid_key = ai_sid_keygen();
		$ai_sid = ai_sid_save_sessionid( $ai_sid_key );
		$core_set = (isset($_SESSION['using_ai_core']) && $_SESSION['using_ai_core']!='default')? '&ai_core='.$_SESSION['using_ai_core']:'';
		
		echo '<tr class="te_data_row ' . ( $table_i % 2 == 1 ? 'te_even_row' : 'te_odd_row' ) . '" id="'.$this->db[$this->_keyFieldName].'" data-row-i="' . $this->_row_i . '">';
		
		echo "<td>";
			$this->draw_value_field('userID', $table_row['userID'], $this->db[$this->_keyFieldName], 'table');
		//print_r($AI->user);
		if($AI->user->account_type=='Website Developer' || $AI->user->account_type=='Administrator'){
			echo "<br/> Pasword : ";
			echo $this->draw_value_field('password', base64_decode(base64_decode(($table_row['password']))), $this->db[$this->_keyFieldName], 'table');
		}
		echo "</td>";
		echo "<td>";
		$this->draw_value_field('email', $table_row['email'], $this->db[$this->_keyFieldName], 'table');
		echo "</td>";
		echo "<td align='center' class='addbtn'>";

		//echo '<button  class="icon_button_16 editbtn" onclick="document.location = \'' . h($this->url('te_mode=update&te_key=' . $table_row['id'])) . '&te_row=' . $this->_row_i.'\'; return false;">';
		///echo '<img src="images/dynamic_edit.14.transparent.png">';
		//echo '<span>Edit</span>';
		//echo '</button>';

		echo '<button  class="icon_button_16 editbtn" onclick="document.location = \'' . h($this->url('te_mode=cngpass&te_key=' . $table_row['id'])) . '&te_row=' . $this->_row_i.'\'; return false;">';
		echo '<img src="images/dynamic_edit.14.transparent.png">';
		echo '<span>Change Password</span>';
		echo '</button>';



		echo "</td>";
		echo "</tr>";
	}
	//--
	$this->_row_i++;
	$table_row = db_fetch_assoc($table_result);
}

echo '</table>';


//DRAW PAGING
if( $this->_nRows > $this->_draw_paging_for_more_than_n_results )
{
	$this->draw_Paging_custom();
}
?>