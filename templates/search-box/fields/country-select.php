<?php
$slocation = isset( $_GET['location'] ) ? sanitize_text_field( $_GET['location'] ): opalestate_get_session_location_val();
Opalestate_Taxonomy_Location::dropdown_list( $slocation );
