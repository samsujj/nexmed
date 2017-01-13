
<?php
	//Copyright (c)2006 All Rights Reserved - Apogee Design Inc.
	//Generated: 2012-05-22 15:24:18 by jon
	//DB Table: share_links, Unique ID: share_links, PK Field: id
	
	global $AI;

	// Grab all stylesheets again
	require(ai_cascadepath('includes/stylesheets.php'));

	$AI->skin->css('includes/plugins/share_links/share_links.css');

	$css_src = $AI->skin->get_css_source();
	if($css_src != '') {
		echo '<link rel="stylesheet" type="text/css" href="'.$css_src.'" />' . "\n";
	}

	$js_src = $AI->skin->get_js_source();
	if($js_src != '') {
		echo '<script type="text/javascript" src="'.$js_src.'"></script>' . "\n";
	}
	
	require_once( ai_cascadepath('includes/plugins/ajax/ajax.require_once.php') );

	?>
	<link href="includes/modules/share_links/share_links.css" rel="stylesheet" />
	<form id="share_links_form" class="te" method="post" action="<?php echo htmlspecialchars($postURL); ?>" onsubmit="return check_share_links_form( this );" >
		<div class="landing_page_control_bar">
			<div class="landing_page_controls">
				<?php if( $this->write_error_msg != '' ){ ?><div class="error">Error: <?php echo htmlspecialchars( $this->write_error_msg ); ?></div><?php } ?>
				<?php
					$te_share_links = $this;
					include(ai_cascadepath("includes/modules/share_links/includes/draw.admin_bar.php"));
				?>
			</div>
		</div>
		<div align="center">
			<iframe class="landing_page_preview" style="display: none" src="/screenshot?ai_skin=full_page"></iframe>
		</div>
		<script type="text/javascript" src="includes/modules/share_links/share_links.js"></script>
		<script type="text/javascript">
			$( document ).ready(function() {
				<?
				if(!empty($this->db['template_id'])) {
					echo "load_template('" . $this->db['template_id'] . "');\n";
				} else {
					echo "update_height();\n";
				}
				?>
				update_url();
				var hash = parseInt(location.hash.replace(/#/g,""));
				if(hash > 0) {
					goto_page(hash);
				} else {
					goto_page(1);
				}
			});
			
		</script>
	</form>
<?
	require( ai_cascadepath('includes/cleanup.php') );
?>