<?php
/*
Plugin Name: Matty Default Post Thumbnail
Plugin URI: http://matty.co.za/
Description: Select an image from your WordPress "Media Library", to be used as the default post thumbnail image when no post thumbnail image has been selected.
Version: 1.0.0
Author: Matty
Author URI: http://matty.co.za/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
/*  Copyright 2012  Matty

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Instantiate class.
new Matty_Default_Post_Thumbnail();

class Matty_Default_Post_Thumbnail {
	/**
	 * Constructor.
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct () {
		add_filter( 'post_thumbnail_html', array( &$this, 'get_default_thumbnail' ), 1, 5 );
		add_action( 'admin_init', array( &$this, 'register_default_thumbnail_selector' ) );
	} // End __construct()

	/**
	 * Filter the HTML for the post thumbnail, replacing it with the default image.
	 * @since  1.0.0
	 * @param  string $html              Existing HTML code for outputting the featured image.
	 * @param  int $post_id           	 The ID of the post to which this featured image is attached.
	 * @param  int $post_thumbnail_id  	 The ID of the post thumbnail, in the database.
	 * @param  array/string $size        The size, as defined either by an array or a defined string for the size.
	 * @param  array $attr               Attributes to assign to the featured image HTML.
	 * @return string                    Featured image HTML code.
	 */
	public function get_default_thumbnail ( $html, $post_id, $post_thumbnail_id, $size, $attr  ) {
		if ( is_admin() && ( (bool)get_option( 'matty_default_thumbnail_filter_admin', '0' ) == false ) ) return $html;

		if ( $html == '' ) {
			$default_id = get_option( 'matty_default_thumbnail_id', '0' );
			if ( intval( $default_id ) > 0 ) {
				$html = wp_get_attachment_image( intval( $default_id ), $size, false, $attr );
			}
		}

		return $html;
	} // End get_default_thumbnail()

	/**
	 * Register the settings section and settings fields on the "Media" settings screen.
	 * @since  1.0.0
	 * @return void
	 */
	public function register_default_thumbnail_selector () {
		add_settings_section( 'matty-default-thumbnail', __( 'Default Thumbnail', 'matty-default-post-thumbnail' ), array( &$this, 'thumbnail_section' ), 'media' );
		add_settings_field( 'matty_default_thumbnail_id', __( 'Select the default thumbnail', 'matty-default-post-thumbnail' ), array( &$this, 'default_thumbnail_select_box' ), 'media', 'matty-default-thumbnail', array( 'label_for' => 'matty_default_thumbnail_id' ) );
		add_settings_field( 'matty_default_thumbnail_filter_admin', '', array( &$this, 'filter_on_admin_checkbox' ), 'media', 'matty-default-thumbnail', array( 'label_for' => 'matty_default_thumbnail_filter_admin' ) );

		register_setting( 'matty-default-thumbnail', 'matty_default_thumbnail_id', 'intval' );
		register_setting( 'matty-default-thumbnail', 'matty_default_thumbnail_filter_admin', array( &$this, 'default_thumbnail_check_bool' ) );
	} // End register_default_thumbnail_selector()

	/**
	 * Output the fields for this settings section.
	 * @since  1.0.0
	 * @return void
	 */
	public function thumbnail_section () {
		settings_fields( 'matty-default-thumbnail' );
	} // End thumbnail_section()

	public function default_thumbnail_check_bool ( $value ) {
		if ( $value != '1' ) { $value = '0'; }

		$value = (bool)$value;

		return $value;
	} // End default_thumbnail_check_bool()

	/**
	 * Generate a select box, containing a list of all images in the "Media Library".
	 * @since  1.0.0
	 * @param  array $args An array of arguments, pertaining to this field.
	 * @return void
	 */
	public function default_thumbnail_select_box ( $args ) {
		$default = get_option( 'matty_default_thumbnail_id', '0' );
		$attachments = get_posts( array( 'post_type' => 'attachment', 'post_mime_type' => 'image', 'numberposts' => -1 ) );

		$field = '<select name="matty_default_thumbnail_id" id="matty_default_thumbnail_id">' . "\n";
		if ( ! is_wp_error( $attachments ) && count( $attachments ) > 0 ) {
			foreach ( $attachments as $k => $v ) {
				$field .= '<option value="' . esc_attr( intval( $v->ID ) ) . '"' . selected( intval( $default ), intval( $v->ID ), false ) . '>' . esc_attr( $v->post_title ) . '</option>' . "\n";
			}
		}
		$field .= '</select>' . "\n";

		echo $field;
	} // End default_thumbnail_select_box()

	/**
	 * Generate a checkbox, for whether or not to filter the images within the WordPress admin.
	 * @since  1.0.0
	 * @param  array $args An array of arguments, pertaining to this field.
	 * @return void
	 */
	public function filter_on_admin_checkbox ( $args ) {
		$default = get_option( 'matty_default_thumbnail_filter_admin', '0' );
		
		$field = '<input type="checkbox" name="matty_default_thumbnail_filter_admin" id="matty_default_thumbnail_filter_admin" value="1"' . checked( $default, '1', false ) . ' />' . "\n";

		$field .= '<label for="' . esc_attr( $args['label_for'] ) . '">' . __( 'Apply the post thumbnail HTML filter within the admin?', 'matty-default-post-thumbnail' ) . '</label>';

		echo $field;
	} // End filter_on_admin_checkbox()
} // End Class
?>