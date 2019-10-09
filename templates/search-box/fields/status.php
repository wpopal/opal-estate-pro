<?php
$status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ): -1;
Opalestate_Taxonomy_Status::dropdown_list( $status );
