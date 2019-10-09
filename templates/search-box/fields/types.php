<?php
$stypes = isset( $_GET['types'] ) ? $_GET['types'] : -1;

if ( isset( $ismultiple ) ) {
	Opalestate_Taxonomy_Type::get_multi_check_list( $stypes );
} else {
	Opalestate_Taxonomy_Type::dropdown_list( $stypes );
}
