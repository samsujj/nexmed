<?php
	//Copyright (c)2006 All Rights Reserved - Apogee Design Inc.
	//Generated: 2012-05-22 15:24:18 by jon
	//DB Table: share_links, Unique ID: share_links, PK Field: id

	if( $this->te_mode != 'delete' )
	{
		$prev_key = $this->find_prev_key( false );
		$next_key = $this->find_next_key( false );

		?>
			<div class="te te_viewnav">
				<?php
					if( $this->te_permit['table'] ){ ?><a class="te back" href="<?php echo htmlspecialchars($this->url( 'te_mode=table' )); ?>"><span class="te back">Back To List</span></a><?php }
					if( $this->te_permit['update'] ){ ?><a class="te edit" href="<?php echo htmlspecialchars($this->url( 'te_mode=update&te_key=' . $this->te_key )); ?>"><span class="te edit">Edit</span></a><?php }
					if( $this->te_permit['copy'] ){ ?><a class="te copy" href="<?php echo htmlspecialchars($this->url( 'te_mode=copy&te_key=' . $this->te_key )); ?>"><span class="te copy">Copy</span></a><?php }
					if( $this->te_permit['insert'] ){ ?><a class="te new" href="<?php echo htmlspecialchars($this->url( 'te_mode=insert' )); ?>"><span class="te new">New</span></a><?php }
					if( $this->te_permit['delete'] ){ ?><a class="te delete" href="<?php echo htmlspecialchars($this->url( 'te_mode=delete&te_key=' . $this->te_key )); ?>"><span class="te delete">Delete</span></a><?php }
					if( $this->te_permit['view'] && $prev_key != $this->te_key ){ ?><a class="te prev" href="<?php echo htmlspecialchars($this->url( 'te_mode=view&te_key=' . $this->te_key . '&te_prev_key=' . $prev_key )); ?>"><span class="te prev">Prev</span></a><?php }
					if( $this->te_permit['view'] && $next_key != $this->te_key ){ ?><a class="te next" href="<?php echo htmlspecialchars($this->url( 'te_mode=view&te_key=' . $this->te_key . '&te_next_key=' . $next_key )); ?>"><span class="te next">Next</span></a><?php }
				?>
			</div>
		<?php
	}
	else
	{
		?>
			<div class="te te_delete_form">
				<form class="te te_delete_form" name="frm" method="post" action="<?php echo htmlspecialchars($this->url( "te_mode=delete&te_key=" . $this->te_key )); ?>">
					<input class="confirm_buttondel" type="submit" name="btnConfirm" value="Confirm Delete" />
					<input class="confirm_buttondel" type="button" name="btnCancel" value="Cancel" onclick="document.location='<?php echo htmlspecialchars($this->url( 'blog-video-category' )); ?>';" />
				</form>
			</div>
		<?php
	}
?>