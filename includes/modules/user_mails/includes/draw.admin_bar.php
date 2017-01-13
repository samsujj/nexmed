<?
global $AI;
?>
<table width="1000" style="margin: 15px 0 5px 0">
	<tr>
		<td align="left" style="text-align: left;" width="30%">
			<button class="primary" onclick="document.location='<?php echo AI_CURRENT_URL . 'share_links'; ?>'; return false;">Back to Dashboard</button>
		</td>
		<td width="40%">
			<h1 style="margin: 0;">Build a Landing Page</h1>
			<span style="font-size: 12px">Step <span id="landing_page_step">1</span> of 3</span>
		</td>
		<td align="right" style="text-align: right;" width="30%">
			<button class="primary" onclick="window.open('http://www.radius-360.com/contact.php','_blank'); return false;">Request Custom Design</button>
		</td>
	</tr>
	<!-- Step 1 -->
	<tr class="landing_page_step_1">
		<td align="center" colspan="3" style="text-align: center">
			<?php $te_share_links->draw_input_field( 'url', $te_share_links->db['url'], 'edit', 'url' ); ?>

			<?
			// Hidden Fields
			$fields = array("Name","Description","Template ID","IMG URL","Owner ID");
			
			if(empty($te_share_links->db['id']) || empty($te_share_links->db['name'])) { $te_share_links->db['name'] = "Landing Page: " . $AI->user->username; }
			if(empty($te_share_links->db['id']) || empty($te_share_links->db['description'])) { $te_share_links->db['description'] = "Landing Page: " . $AI->user->username; }
			if(empty($te_share_links->db['id']) || empty($te_share_links->db['owner_id'])) { $te_share_links->db['owner_id'] = $AI->user->userID; }
			
			if(!empty($fields)) {
				foreach($fields as $field) {
					echo '<input type="hidden" name="' . str_replace(" ","_",strtolower($field)) . '" id="' . str_replace(" ","_",strtolower($field)) . '" value="' . $te_share_links->db[str_replace(" ","_",strtolower($field))] . '" />';
				}
			}
			?>

		</td>
	</tr>
	<tr class="landing_page_step_1">
		<td align="left" style="text-align: left;" class="button_prev">
			<button class="icon_button">
				<table width="200" align="center">
					<tr>
						<td rowspan="2" width="32"><img src="images/menu_tree/28.png" width="32"></td>
						<td align="center">Previous Step</td>
					</tr>
					<tr>
						<td align="center" class="small_text">Edit Name</td>
					</tr>
				</table>
			</button>
		</td>
		<td></td>
		<td align="right" style="text-align: right;" class="button_next">
			<button class="icon_button">
				<table width="200" align="center">
					<tr>
						<td align="center">Next Step</td>
						<td rowspan="2" width="32"><img src="images/menu_tree/23.png" width="32"></td>
					</tr>
					<tr>
						<td align="center" class="small_text">Select Template</td>
					</tr>
				</table>
			</button>
		</td>
	</tr>
	
	<!-- Step 2 -->
	<tr class="landing_page_step_2">
		<td align="center" colspan="3">
			Select Template
			<?
				$no_cache = (isset($_GET['no_cache']) ? "no_cache&" : "");
				$core_set = (isset($_SESSION['using_ai_core']) && $_SESSION['using_ai_core']!='default')? '&ai_core='.$_SESSION['using_ai_core']:'';
				$selected_template = $te_share_links->db['template_id'];

				$landing_pages = $AI->db->getAll("SELECT id FROM landing_page_templates WHERE show_hide = 'Show' ORDER BY id;");
				if(!empty($landing_pages)) {
					echo "<table width=\"100%\" style=\"margin: 15px 0 5px 0\">\n";
					echo "<tr>\n";
					foreach($landing_pages as $lp) {
						$webshots_url = "//webshots.apogeevault.com/webshots/?" . $no_cache . "w=125&url=" . urlencode(AI_CURRENT_URL . "screenshot?selected_template=" . $lp['id'] . $core_set);
						echo "<td>\n";
						echo "<a href=\"javascript:void(0)\" onclick=\"load_template('" . $lp['id'] . "');\" id=\"screenshot_" . $lp['id'] . "\" class=\"single_thumbnail ";
						if($lp['id'] == $selected_template) { echo " single_selected_thumbnail"; }
						echo "\" style=\"background: url('" . $webshots_url . "') top center;\"></a>\n";
						echo "</td>\n";
					}
					echo "</tr>\n";
					echo "</table>\n";								
				}
			?>
		</td>
	</tr>
	<tr class="landing_page_step_2">
		<td align="left" style="text-align: left;" class="button_prev">
			<button class="icon_button">
				<table width="200" align="center">
					<tr>
						<td rowspan="2" width="32"><img src="images/menu_tree/28.png" width="32"></td>
						<td align="center">Previous Step</td>
					</tr>
					<tr>
						<td align="center" class="small_text">Edit Name</td>
					</tr>
				</table>
			</button>
		</td>
		<td></td>
		<td align="right" style="text-align: right;" class="button_next">
			<button class="icon_button">
				<table width="200" align="center">
					<tr>
						<td align="center">Next Step</td>
						<td rowspan="2" width="32"><img src="images/menu_tree/23.png" width="32"></td>
					</tr>
					<tr>
						<td align="center" class="small_text">Edit Page</td>
					</tr>
				</table>
			</button>
		</td>
	</tr>
	
	<!-- Step 3 -->
	<tr class="landing_page_step_3">
		<td align="center" colspan="3">
			<br>
		</td>
	</tr>
	<tr class="landing_page_step_3">
		<td align="left" style="text-align: left;" class="button_prev">
			<button class="icon_button" onclick="if(confirm('Are you sure you wish to go back, any unsaved changes will be lost!!')) { document.location='<?=AI_CURRENT_URL?>share_links?te_class=share_links&te_mode=update&te_key=<?=$te_share_links->db['id']?>&te_row=<?=(int) @$te_share_links->_row_i?>#2' }">
				<table width="200" align="center">
					<tr>
						<td rowspan="2" width="32"><img src="images/menu_tree/28.png" width="32"></td>
						<td align="center">Previous Step</td>
					</tr>
					<tr>
						<td align="center" class="small_text">Edit Template</td>
					</tr>
				</table>
			</button>
		</td>
		<td valign="middle" style="font-size: 87.5%">Edit Your Landing Page Below<br />Hover Over an Area and Click the Pencil Icon</td>
		<td align="right" style="text-align: right;" class="button_next">
			<?
				if ( isset($te_share_links->gets['te_row']) && is_numeric($te_share_links->gets['te_row']) )
				{
					$te_share_links->_row_i = (int) $te_share_links->gets['te_row'];
				}
			?>
			<button class="icon_button" style="font-size: 24px; line-height: 32px;" onclick="document.location='<?=AI_CURRENT_URL?>share_links?te_class=share_links&te_mode=table&te_key=<?=$te_share_links->db['id']?>&te_row=<?=(int) @$te_share_links->_row_i?>'">
				<img src="images/leads/green-check.png" width="32" style="float: left"> Done
			</button>
		</td>
	</tr>
</table>
<script type="text/javascript">
	function check_url() {
		$("#url_sub_1").addClass("loading");
		var url = encodeURIComponent($("#url").val());
		var domain_id = $("#domain_id").val();
		var sub_domain_id = $("#sub_domain_id").val();
		
		ajax_get_request('<?=AI_CURRENT_URL?>share_links?ai_skin=full_page&te_class=share_links&te_mode=ajax&ajax_cmd=check_url&id=<?=$te_share_links->db['id']?>&url='+url+'&domain_id='+domain_id+'&sub_domain_id='+sub_domain_id, ajax_handler_default);
	}
</script>
