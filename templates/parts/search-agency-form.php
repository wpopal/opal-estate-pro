<?php
	$fields 		= OpalEstate_Search::get_setting_search_fields(); 
	$slocation  	= isset($_GET['location'])? sanitize_text_field( $_GET['location'] ): opalestate_get_session_location_val();  
	$search_text 	= isset($_GET['search_text'])? sanitize_text_field( $_GET['search_text'] ):'';

	if( isset($current_uri) && $current_uri == true ) { 
		$uri = opalestate_get_current_uri(); 
	} else {
		$uri = opalestate_search_agency_uri();
	}

?>
<form id="opalestate-search-agency-form" class="opalestate-search-agency-form opalestate-search-form" action="<?php echo esc_url( $uri ); ?>" method="get">
	
		<div class="<?php echo apply_filters('opalestate_row_container_class', 'opal-row');?>">
			<div class="col-lg-4 col-md-4 col-sm-4">
				<?php Opalestate_Taxonomy_Location::dropdown_list( $slocation );?>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6">
				 <label class="opalestate-label opalestate-label--geo-location"><?php esc_html_e( 'Agency', 'opalestate-pro' ); ?></label>
				<input name="search_text" value="<?php echo esc_attr($search_text); ?>" maxlength="40" class="form-control input-large input-search" size="20" placeholder="<?php esc_html_e( 'Enter Agency Name', 'opalestate-pro' ); ?>" type="text">
			</div>
 
			<div class="col-lg-2 col-md-2 col-sm-2">
				<input type="hidden" name="s_agency" value="1">
				<button type="submit" class="btn btn-secondary btn-block btn-search btn-3d">
					<i class="fa fa-search"></i>
					<span><?php esc_html_e('Search','opalestate-pro'); ?></span>
				</button>
			</div>
		</div>
</form>
