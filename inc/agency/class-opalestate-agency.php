<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @class   OpalEstate_Agency
 *
 * @version 1.0
 */
class OpalEstate_Agency {

	/**
	 * @var String $author_name
	 *
	 * @access protected
	 */
	protected $author_name;

	/**
	 * @var Boolean $is_featured
	 *
	 * @access protected
	 */
	protected $is_featured;

	/**
	 * Get A Instance Of Opalestate_Property
	 */
	public static function get_instance( $post_id = null ) {
		static $_instance;
		if ( ! $_instance ) {
			$_instance = new OpalEstate_Agency( $post_id );
		}

		return $_instance;
	}


	/**
	 *  Constructor
	 */
	public function __construct( $post_id = null ) {

		global $post;

		$this->post        = $post;
		$this->post_id     = $post_id ? $post_id : get_the_ID();
		$this->author      = get_userdata( $post->post_author );
		$this->author_name = ! empty( $this->author ) ? sprintf( '%s %s', $this->author->first_name, $this->author->last_name ) : null;
		$this->is_featured = $this->get_meta( 'featured' );
		$this->is_trusted  = $this->get_meta( 'trusted' );
	}

	public function get_id() {
		return $this->post_id;
	}

	/**
	 * Get Collection Of soicals with theirs values
	 */
	public function get_socials() {
		$socials = [
			'facebook'  => '',
			'twitter'   => '',
			'pinterest' => '',
			'google'    => '',
			'instagram' => '',
			'linkedIn'  => '',
		];

		$output = [];

		foreach ( $socials as $social => $k ) {

			$data = $this->get_meta( $social );
			if ( $data && $data != "#" && ! empty( $data ) ) {
				$output[ $social ] = $data;
			}
		}

		return $output;
	}

	/**
	 * Get url of user avatar by agency id
	 */
	public static function get_avatar_url( $userID ) {

		return get_post_meta( $userID, OPALESTATE_AGENCY_PREFIX . "avatar", true );

	}

	/**
	 * Render list of levels of agency
	 */
	public function render_level() {
		$levels = wp_get_post_terms( $this->post_id, 'opalestate_agency_cat' );

		if ( empty( $levels ) ) {
			return;
		}

		$output = '<span class="agency-levels">';
		foreach ( $levels as $key => $value ) {
			$output .= '<span class="agency-label"><span>' . $value->name . '</span></span>';
		}
		$output .= '</span>';

		echo $output;
	}

	/**
	 * get meta data value of key without prefix
	 */
	public function get_meta( $key ) {
		return get_post_meta( $this->get_id(), OPALESTATE_AGENCY_PREFIX . $key, true );
	}


	/**
	 *  return true if this agency is featured
	 */
	public function is_featured() {
		return $this->is_featured;
	}


	public function render_avatar() {

	}

	/**
	 *  render block information by id
	 */
	public static function render_box_info( $post_id ) {

	}


	public function get_gallery() {
		return $this->get_meta( 'gallery' );
	}


	public function get_trusted() {
		return $this->is_trusted;
	}

	public function get_members() {
		$team = [];
		$ids  = get_post_meta( $this->post_id, OPALESTATE_AGENCY_PREFIX . 'team', true );

		foreach ( $ids as $id ) {
			$user   = get_user_by( 'id', $id ); // echo '<pre>' . print_r( $user, 1 );die;
			$team[] = [
				'id'          => $user->ID,
				'name'        => $user->display_name,
				'avatar_url'  => OpalEstate_User::get_author_picture( $user->ID ),
				'username'    => $user->user_login,
				'description' => 'okokok',
			];
		}

		return $team;
	}

	public static function get_link( $agency_id ) {
		$agency = get_post( $agency_id );
		$url    = self::get_avatar_url( $agency_id );

		return [
			'name'   => $agency->post_title,
			'avatar' => $url,
			'link'   => get_permalink( $agency->ID ),
		];
	}

	public static function metaboxes_fields() {
		$metabox = new Opalestate_Agency_MetaBox();
		$fields  = $metabox->metaboxes_admin_fields();

		return array_merge_recursive( $fields, $metabox->get_social_fields( OPALESTATE_AGENCY_PREFIX ) );
	}

	/**
	 * Get rating count.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return int
	 */
	public function get_rating_counts() {
		return $this->get_meta( 'rating_count' ) ? $this->get_meta( 'rating_count' ) : 0;
	}

	/**
	 * Get average rating.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return float
	 */
	public function get_average_rating() {
		return $this->get_meta( 'average_rating' ) ? $this->get_meta( 'average_rating' ) : 0;
	}

	/**
	 * Get review count.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return int
	 */
	public function get_review_count() {
		return $this->get_meta( 'review_count' ) ? $this->get_meta( 'review_count' ) : 0;
	}

	public function get_rating_count_stats() {
		return $this->get_meta( 'rating_count_stats' ) ? $this->get_meta( 'rating_count_stats' ) : [
			5 => 0,
			4 => 0,
			3 => 0,
			2 => 0,
			1 => 0,
		];
	}

	public function get_rating_average_stats() {
		return $this->get_meta( 'rating_average_stats' );
	}

	/**
	 *
	 */
	public static function update_user_data( $user_id ) {

		$fields = self::metaboxes_fields();

		$others = [
			'avatar_id' => '',
			'map'       => '',
		];

		foreach ( $fields as $key => $field ) {
			$kpos = $field['id'];
			$tmp  = str_replace( OPALESTATE_AGENCY_PREFIX, "", $field['id'] );
			if ( isset( $_POST[ $kpos ] ) && $tmp ) {
				$data = is_string( $_POST[ $kpos ] ) ? sanitize_text_field( $_POST[ $kpos ] ) : $_POST[ $kpos ];
				update_user_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . $tmp, $data );
			}
		}

		// update for others 
		foreach ( $others as $key => $value ) {
			$kpos = OPALESTATE_AGENCY_PREFIX . $key;
			if ( isset( $_POST[ $kpos ] ) ) {
				$data = is_string( $_POST[ $kpos ] ) ? sanitize_text_field( $_POST[ $kpos ] ) : $_POST[ $kpos ];
				update_user_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . $key, $data );
			}
		}
	}

	/**
	 *
	 */
	public static function update_data_from_user( $related_id ) {


		$fields = self::metaboxes_fields();

		$others = [
			'avatar_id' => '',
			'map'       => '',
		];
		foreach ( $fields as $key => $field ) {

			$tmp  = str_replace( OPALESTATE_AGENCY_PREFIX, "", $field['id'] );
			$kpos = OPALESTATE_USER_PROFILE_PREFIX . $tmp;

			if ( isset( $_POST[ $kpos ] ) && $tmp ) {
				$data = is_string( $_POST[ $kpos ] ) ? sanitize_text_field( $_POST[ $kpos ] ) : $_POST[ $kpos ];
				update_post_meta( $related_id, OPALESTATE_AGENCY_PREFIX . $tmp, $data );
			}
		}

		// update for others 
		foreach ( $others as $key => $value ) {
			$kpos = OPALESTATE_USER_PROFILE_PREFIX . $key;
			if ( isset( $_POST[ $kpos ] ) ) {
				$data = is_string( $_POST[ $kpos ] ) ? sanitize_text_field( $_POST[ $kpos ] ) : $_POST[ $kpos ];
				update_post_meta( $related_id, OPALESTATE_AGENCY_PREFIX . $key, $data );
			}
		}
	}
}
