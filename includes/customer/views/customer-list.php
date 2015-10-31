<div class="wrap">
	<h2><?php _e( 'Customers', 'ac' ); ?> <?php echo sprintf( '<a href="?page=%s&action=%s" class="add-new-h2">Add New</a>',  esc_attr( $_REQUEST['page'] ), 'new' ); ?></h2>

	<form method="post">
		<input type="hidden" name="page" value="customers">
		<?php
		$this->customer_obj->prepare_items();
		$this->customer_obj->search_box( 'Search', 'customers' );
		$this->customer_obj->display(); ?>
	</form>
</div>
