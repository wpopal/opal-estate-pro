<?php
	$fields = OpalEstate_Search::get_setting_search_fields(); 
 
	$stypes 	= isset($_GET['types'])? sanitize_text_field( $_GET['types'] ) : -1;
	 
	if( isset($current_uri) && $current_uri == true ) { 
		$uri = opalestate_get_current_uri(); 
	} else {
		$uri = opalestate_search_agent_uri();
	}
?>
<form id="opalestate-search-agency-form" class="opalestate-search-agency-form opalestate-search-form" action="<?php echo esc_attr( $uri ); ?>" method="get">
	
		<div class="<?php echo apply_filters('opalestate_row_container_class', 'opal-row');?>">
			<div class="col-lg-6 col-md-6 col-sm-12">
				<p class="search-agent-title"><?php esc_html_e( 'Find an experienced agent with:' ,'opalestate-pro'); ?></p>
			</div>
			<div class="col-lg-6 col-md-6 hidden-sm hidden-xs">
				<p class="search-agent-title hide"><?php esc_html_e( 'Who sale between:' ,'opalestate-pro'); ?></p>
			</div>
		</div>
		
		<div class="<?php echo apply_filters('opalestate_row_container_class', 'opal-row');?>">
			<div class="col-lg-7 col-md-3 col-sm-7">
				<?php echo opalestate_load_template_path( 'search-box/fields/search-city-text' ); ?>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3">
				
				<?php  Opalestate_Taxonomy_Type::dropdown_list( $stypes ); ?>
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