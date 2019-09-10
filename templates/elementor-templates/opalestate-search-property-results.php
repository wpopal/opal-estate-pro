<?php 
	$display = array(
		'style'	 	=> $settings['style'],
		'column'	=> $settings['column']
	);
// echo time().'<pre> ha congtein' . print_r( $display ,1 ).'</pre>';
 

?>
<div class="opalesate-properties-ajax opalesate-properties-results" data-mode="html">

<?php echo opalestate_load_template_path( 'shortcodes/ajax-map-search-result' , $display ); ?>

</div>