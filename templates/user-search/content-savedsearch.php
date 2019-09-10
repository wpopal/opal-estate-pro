<?php
$data = OpalEstate_User_Search::get_instance()->get_list();
// echo '<pre>'.print_r( $data,  1 );die;
?>
<?php if( $data ): ?>
<div class="property-listing my-saved-search">
 	<div class="opalestate-admin-box">
 		<div class="box-content">
 			<h4><?php esc_html_e( 'My Saved Searches' , 'opalestate-pro' ) ; ?></h4>			
			<table class="table table-condensed">
	 			<thead> 
	 			 	<tr> <th>#</th> <th><?php esc_html_e('Name','opalestate-pro'); ?></th> <th><?php esc_html_e('View','opalestate-pro'); ?></th> <th><?php esc_html_e('Delete','opalestate-pro'); ?></th>  </tr>
	 			</thead> 
	 				<tbody> 
						
						<?php  foreach( $data as $key => $search ):  ?>

			 				<tr> 
			 					<th scope="row"><?php echo $key + 1; ?></th> 
			 					<td><?php echo $search->name; ?></td>
			 				 	<td><a target="_blank" href="<?php echo opalestate_get_search_link().'?'.$search->params; ?>"> <i class="fa fa-search"></i></a></td>  
			 				 	<td><a class="text-danger" onclick="return confirm('<?php esc_html_e( 'Are you sure to delete this?', 'opalestate-pro' ); ?>')" href="<?php echo opalestate_user_savedsearch_page( array('id' => $search->id ,'doaction' =>'delete') ); ?>"> <i class="fa fa-close"></i></a></td>  
			 				</tr> 

					 	<?php endforeach; ?>
		 			</tbody> 
		 	</table>
 

		</div>	
 	</div>
</div>
<?php else : ?>
	<div class="opalestate-box">	
	 	<div class="box-content">
		 	<div class="opalestate-message">
		 		<h3><?php esc_html_e( 'No Item In Saved Searches', 'opalestate-pro' ); ?></h3>
				<p><?php esc_html_e( 'You have not added any search data.', 'opalestate-pro' ) ;?></p>
			</div>
		</div>	
	</div>	
<?php endif; ?>
<?php wp_reset_postdata(); ?>