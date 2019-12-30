<?php
$scategories = isset( $_GET['cat'] ) ? $_GET['cat'] : -1;

if ( isset( $ismultiple ) ) {
	Opalestate_Taxonomy_Categories::get_multi_check_list( $scategories );
} else {
	Opalestate_Taxonomy_Categories::dropdown_list( $scategories );
}
