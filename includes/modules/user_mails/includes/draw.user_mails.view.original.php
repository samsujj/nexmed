<?php
	//Copyright (c)2006 All Rights Reserved - Apogee Design Inc.
	//Generated: 2012-05-22 15:24:18 by jon
	//DB Table: share_links, Unique ID: share_links, PK Field: id
	require_once( ai_cascadepath('includes/plugins/ajax/ajax.require_once.php') );
?>
<div class="te_view share_links_view">

	<fieldset class="te">
		<legend class="te">
			<a class="te" href="<?php echo ( $this->te_permit['table'] ? htmlspecialchars($this->url('te_mode=table')) :'#'); ?>"><?php echo htmlspecialchars( $this->_tableTitle ); ?></a>
			:
			<?php
				switch( $this->te_mode )
				{
					case 'delete': echo 'Confirm Delete'; break;
					default: echo 'View'; break;
				}
			?>
		</legend>

		<div class="te_viewnav_top">
			<?php $this->draw_ViewNav(); ?>
		</div>

		<dl class="te">
		<dt class="te <?php echo $this->get_field_type( 'id' ); ?> id" >ID</dt>
		<dd id="<?php echo 'value_field_container_' . $this->te_key . '_id' ?>" class="te <?php echo $this->get_field_type( 'id' ); ?> id" >
			<?php $this->draw_value_field( 'id', $this->db['id'].'', $this->te_key, 'view' ); ?>
		</dd>
		<dt class="te <?php echo $this->get_field_type( 'name' ); ?> name" >Name</dt>
		<dd id="<?php echo 'value_field_container_' . $this->te_key . '_name' ?>" class="te <?php echo $this->get_field_type( 'name' ); ?> name" >
			<?php $this->draw_value_field( 'name', $this->db['name'].'', $this->te_key, 'view' ); ?>
		</dd>
		<dt class="te <?php echo $this->get_field_type( 'description' ); ?> description" >Description</dt>
		<dd id="<?php echo 'value_field_container_' . $this->te_key . '_description' ?>" class="te <?php echo $this->get_field_type( 'description' ); ?> description" >
			<?php $this->draw_value_field( 'description', $this->db['description'].'', $this->te_key, 'view' ); ?>
		</dd>
		<dt class="te <?php echo $this->get_field_type( 'url' ); ?> url" >URL</dt>
		<dd id="<?php echo 'value_field_container_' . $this->te_key . '_url' ?>" class="te <?php echo $this->get_field_type( 'url' ); ?> url" >
			<?php $this->draw_value_field( 'url', $this->db['url'].'', $this->te_key, 'view' ); ?>
		</dd>
		<dt class="te <?php echo $this->get_field_type( 'img_url' ); ?> img_url" >Image URL</dt>
		<dd id="<?php echo 'value_field_container_' . $this->te_key . '_img_url' ?>" class="te <?php echo $this->get_field_type( 'img_url' ); ?> img_url" >
			<?php $this->draw_value_field( 'img_url', $this->db['img_url'].'', $this->te_key, 'view' ); ?>
		</dd>
		<dt class="te <?php echo $this->get_field_type( 'requires_success_line' ); ?> requires_success_line" >Requires Success Line <small>(Do not display if user is not in success line)</small></dt>
		<dd id="<?php echo 'value_field_container_' . $this->te_key . '_requires_success_line' ?>" class="te <?php echo $this->get_field_type( 'requires_success_line' ); ?> requires_success_line" >
			<?php $this->draw_value_field( 'requires_success_line', $this->db['requires_success_line'].'', $this->te_key, 'view' ); ?>
		</dd>
		<dt class="te <?php echo $this->get_field_type( 'postal_parrot_var_name' ); ?> postal_parrot_var_name" >Postal Parrot Var Name <small>(Merge Code)</small></dt>
		<dd id="<?php echo 'value_field_container_' . $this->te_key . '_postal_parrot_var_name' ?>" class="te <?php echo $this->get_field_type( 'postal_parrot_var_name' ); ?> postal_parrot_var_name" >
			<?php $this->draw_value_field( 'postal_parrot_var_name', $this->db['postal_parrot_var_name'].'', $this->te_key, 'view' ); ?>
		</dd>

		</dl>

		<div class="te_viewnav_bottom">
			<?php $this->draw_ViewNav(); ?>
		</div>

	</fieldset>

</div>
