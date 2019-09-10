<?php
$sstate = isset( $_GET['state'] ) ? $_GET['state'] : '';
Opalestate_Taxonomy_State::dropdown_list( $sstate );
