<?php 	 
$post_id = intval( $_GET['id'] );
$post = get_post( $post_id );
$type = OpalEstate()->session->get( 'submission' );
?>
<div class="opalestate-box-completed alert alert-success">
	<div class="inner">
		<?php if( $type == 'addnew' ): ?>
		<div class="addnew-msg">
			<h5><?php echo esc_html__('Thanks for your submission, it takes some time to review the property.'); ?></h5>
			<?php echo sprintf( esc_html__( 'Click to %s here %s to back to your listing or %s edit %s this.', 'opalestate-pro'),
			 '<a href="'.opalestate_submssion_list_page().'">', '</a>',
			  '<a href="'.opalestate_submssion_page( $post_id ).'">', '</a>'
			); ?>
		</div>	
		<?php else : ?>
		<div class="edit-msg">
			<?php echo esc_html__('Your property is completed success.'); ?>
			<?php echo sprintf( esc_html__( 'Click to %s here %s to back to your listing or %s edit %s this.', 'opalestate-pro'),
			 '<a href="'.opalestate_submssion_list_page().'">', '</a>',
			  '<a href="'.opalestate_submssion_page( $post_id ).'">', '</a>'
			); ?>
		</div>		
		<?php endif; ?>	
	</div>
</div>
<?php OpalEstate()->session->set( 'submission', null ); ?>