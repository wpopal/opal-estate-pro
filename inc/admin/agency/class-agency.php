<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Opalestate_Admin_Agency { 

	/**
	 *
	 */
	public function __construct(){

		add_action( 'cmb2_admin_init', array( $this, 'metaboxes' ) );
		add_action( 'save_post',  array( $this , 'on_save_post'), 13, 2 );
		
		add_action( 'user_register'  , array( $this, 'on_update_user' ), 10, 1 );
		add_action( 'profile_update' , array( $this, 'on_update_user' ), 10, 1 );	
		
	}

	/**
	 * Update relationship post and user data, auto update meta information from post to user
	 */
	public function on_save_post( $post_id ){
		$post_type = get_post_type($post_id);
		if( $post_type == 'opalestate_agency' ){
			if( isset($_POST[OPALESTATE_AGENCY_PREFIX.'user_id']) && $_POST[OPALESTATE_AGENCY_PREFIX.'user_id'] ){ 

				$user_id = absint( $_POST[OPALESTATE_AGENCY_PREFIX.'user_id'] );
				update_user_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', $post_id );
				
				OpalEstate_Agency::update_user_data(  $user_id );
			//	OpalEstate_Agency::update_properties_related(  $user_id );

			}
		}
	}

	/**
	 * Auto update meta information to post from user data updated or created
	 */
	public function on_update_user() {
		if( isset($_POST['user_id'])  && (int) $_POST['user_id'] && isset($_POST['role']) ) {
			if( $_POST['role']  == 'opalestate_agency' ){
				$user_id 	= absint( $_POST['user_id'] );
				$related_id = get_user_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true );

				$post 		= get_post( $related_id );

				if( isset($post->ID) && $post->ID ){
					OpalEstate_Agency::update_data_from_user( $related_id );
				}
			}
		}
	}

	/**
	 *
	 */
	public function metaboxes_fields( $prefix = '' ){
 
		if ( ! $prefix ) {
			$prefix = OPALESTATE_AGENCY_PREFIX;
		}

		$fields =  array(
			
			
			
			array(
			    'name'     => esc_html__('Gallery' ,'opalestate-pro'),
			    'desc'     => esc_html__('Select one, to add new you create in location of estate panel','opalestate-pro'),
			    'id'       => $prefix."gallery",
			    'type'     => 'file_list',
			) ,
			 
			array(
				'name' => esc_html__( 'slogan', 'opalestate-pro' ),
				'id'   => "{$prefix}slogan",
				'type' => 'text'
			)
		);

		return apply_filters( 'opalestate_postype_agency_metaboxes_fields' , $fields );
	}

	/**
	 *
	 */
	public function metaboxes( ){

		global $pagenow; 

		if( ($pagenow == 'post.php' || $pagenow == 'post-new.php') ) {
		
			$prefix = OPALESTATE_AGENCY_PREFIX;

			$metabox = new Opalestate_Agency_MetaBox(); 
			
			$fields = $this->metaboxes_fields(); 
			$fields = array_merge_recursive( $fields , 
				$metabox->get_office_fields( $prefix ),  
				$metabox->get_address_fields( $prefix )
			);


			$box_options = array(
				'id'           => $prefix . 'edit',
				'title'        => esc_html__( 'Metabox', 'opalestate-pro' ),
				'object_types' => array( 'opalestate_agency' ),
				'show_names'   => true,
			);

			// Setup meta box
			$cmb = new_cmb2_box( $box_options );

			// Setting tabs
			$tabs_setting           = array(
				'config' => $box_options,
				'layout' => 'vertical', // Default : horizontal
				'tabs'   => array()
			);


			$tabs_setting['tabs'][] = array(
				'id'     => 'p-general',
				'icon'	 => 'dashicons-admin-home',
				'title'  => esc_html__( 'General', 'opalestate-pro' ),
				'fields' => $fields
			);

			$tabs_setting['tabs'][] = array(
				'id'     => 'p-socials',
				'icon'	 => 'dashicons-admin-home',
				'title'  => esc_html__( 'Socials', 'opalestate-pro' ),
				'fields' => $metabox->get_social_fields( $prefix )
			);


			$tabs_setting['tabs'][] = array(
				'id'     => 'p-target',
				'icon'	 => 'dashicons-admin-tools',
				'title'  => esc_html__( 'Team', 'opalestate-pro' ),
				'fields' => $metabox->metaboxes_target()
			);
			// Set tabs
			$cmb->add_field( array(
				'id'   => '__tabs',
				'type' => 'tabs',
				'tabs' => $tabs_setting
			) );
		}
	}
}
new Opalestate_Admin_Agency();
