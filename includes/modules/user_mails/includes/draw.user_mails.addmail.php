<?php

global $AI;

?>

<?php

	//echo '<button onclick="document.location = \'' . h($this->url('te_mode=insert')) . '\'; return false;">New</button>';


echo "<h2>Add Mail in system</h2>";
echo '<p>&nbsp;</p><!--spacer-->';

echo '<table class="te_main_table pixel_main_table" id="pixel_main_table">';


echo "<tr>";
echo "<th>UserId</th>";
echo "<th>Username</th>";
echo "<th>Name</th>";
echo "<th>Mail</th>";
echo "<th>A/c Type</th>";
echo "<th>Action</th>";
echo "</tr>";


//var_dump($table_result);

$table_row = db_fetch_assoc($table_result);

for ( $table_i = 0; $table_i < $this->_pgSize && $table_row; $table_i++ )
{

	if (true) {

		echo '<tr class="te_data_row ' . ( $table_i % 2 == 1 ? 'te_even_row' : 'te_odd_row' ) . '">';

		echo "<td>";
		echo $table_row['userID'];
		echo "</td>";
		echo "<td>";
		echo $table_row['username'];
		echo "</td>";
		echo "<td>";
		echo $table_row['first_name']." ".$table_row['last_name'];
		echo "</td>";
		echo "<td>";
		echo strtolower($table_row['username'])."@nexmedsolutions.com";
		echo "</td>";
		echo "<td>";
		echo $table_row['account_type'];
		echo "</td>";
		echo "<td align='center' class='addbtn'>";

		echo '<button  class="icon_button_16 editbtn"  onclick="document.location = \'' . h($this->url('te_mode=addimapmail&te_key=' . $table_row['userID'])).'\'; return false;">';
		echo '<img src="images/dynamic_edit.14.transparent.png">';
		echo '<span>Add mail in system</span>';
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
	$this->draw_Paging_custom('addmail');
}
?>