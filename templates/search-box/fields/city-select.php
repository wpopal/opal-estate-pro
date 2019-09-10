<?php
$scity = isset( $_GET['city'] ) ? sanitize_text_field( $_GET['city'] ) : '';
Opalestate_Taxonomy_City::dropdown_list( $scity );
