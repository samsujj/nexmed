<?php
	//Copyright (c)2006 All Rights Reserved - Apogee Design Inc.
	//Generated: 2012-05-22 15:24:18 by jon
	//DB Table: share_links, Unique ID: share_links, PK Field: id
	require_once( ai_cascadepath('includes/plugins/ajax/ajax.require_once.php') );
?>

<div class="te_qsearch share_links_qsearch">
	<form method="get" action="<?php echo htmlspecialchars($this->url( '' )); ?>">
		<input type="hidden" name="te_class" value="<?php echo $this->unique_id; ?>" />
		<input type="hidden" name="te_mode" value="table" />
		<input type="hidden" name="te_qsearch" value="true" />
		<input type="hidden" name="te_qsearchMode" value="<?php echo htmlspecialchars( $this->qsearchMode ); ?>" />

		<div class="te_qsearch_keywords">
			<label class="te te_qkeywords" for="te_qkeywords">Quick Search</label>
			<input id="te_qkeywords" class="te te_qkewords" type="text" name="te_qkeywords" value="<?php echo htmlspecialchars($this->qkeywords);?>" />
			<input class="te te_buttons search_button" type="submit" name="btnSearch"   value="Search" />
			<input class="te te_buttons show_all_button" type="button" name="btnResetSearch" value="Show All" onclick="document.location='<?php echo htmlspecialchars($this->url( 'te_mode=table&te_resetsearch=true' )); ?>';" />
			<?php
				if( $this->te_permit['asearch'] )
				{
					?><a class="te_asearch_link" href="<?php echo htmlspecialchars($this->url( 'te_mode=asearch' )); ?>">Advanced Search</a><?php
				}
			?>
		</div>
		<div class="te_qsearch_results_caption">
			<?php
				//DRAW SEARCH REPORT...
				$search_report = '';

				if( trim($this->qkeywords) != '' )
				{
					$search_report .= 'Keywords: ' . $this->qkeywords;
				}

				foreach( $this->search_vars as $n => $v )
				{
					if( trim($v) != '' )
					{
						if( $search_report != '' ){ $search_report .= ', '; }
						$search_report .= ucwords( str_replace( '_', ' ', strtolower( $n ) ) ) . ': ' . $v;
					}
				}

				if( $search_report != '' )
				{
					echo 'Results For ' . $search_report;
				}
			?>
		</div>
	</form>
</div>
