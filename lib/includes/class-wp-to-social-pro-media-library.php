<?php
/**
 * Media Library class.
 *
 * @package WP_To_Social_Pro
 * @author WP Zinc
 */

/**
 * Handles uploading and deleting images generated by this Plugin.
 *
 * @package WP_To_Social_Pro
 * @author  WP Zinc
 * @version 4.2.0
 */
class WP_To_Social_Pro_Media_Library {

	/**
	 * Holds the base object.
	 *
	 * @since   4.2.0
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor.
	 *
	 * @since   4.2.0
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

	}

	/**
	 * Uploads the given image path and file into the WordPress Media Library
	 *
	 * @since   4.2.0
	 *
	 * @param   string $source     Source Path and Filename.
	 * @param   int    $post_id    Post ID.
	 * @param   string $filename   Target Filename to save source as.
	 * @param   string $title      Image Title (optional).
	 * @param   string $caption    Image Caption (optional).
	 * @param   string $alt_tag    Image Alt Tag (optional).
	 * @param   string $description Image Description (optional).
	 * @return  WP_Error|int        WP_Error | Image ID
	 */
	public function upload_local_image( $source, $post_id = 0, $filename = false, $title = '', $caption = '', $alt_tag = '', $description = '' ) {

		// If GD support is available, enable it now.
		if ( $this->is_gd_available() ) {
			add_filter( 'wp_image_editors', array( $this, 'enable_gd_image_support' ) );
		}

		// Load required image / media classes and functions.
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		// Get image type.
		$type = getimagesize( $source );
		if ( ! isset( $type['mime'] ) ) {
			return new WP_Error(
				sprintf(
					/* translators: Image Source Path and Filename */
					__( 'Could not identify MIME type of source image %s. Is this an image?', 'wp-to-hootsuite' ),
					$source
				)
			);
		}
		list( $type, $ext ) = explode( '/', $type['mime'] );
		unset( $type );

		// Define image filename.
		$file_array['name']     = ( $filename !== false ? $filename : basename( $source ) );
		$file_array['tmp_name'] = $source;

		// Add the extension to the filename if it doesn't exist.
		// This happens if we streamed an image URL e.g. http://placehold.it/400x400.
		if ( strpos( $file_array['name'], '.' . $ext ) === false ) {
			$file_array['name'] .= '.' . $ext;
		}

		// Import the image into the Media Library.
		$image_id = media_handle_sideload( $file_array, $post_id, '' );
		if ( is_wp_error( $image_id ) ) {
			return $image_id;
		}

		// Store the Plugin Name against the Attachment's meta.
		update_post_meta( $image_id, '_' . $this->base->plugin->filter_name, 1 );

		// If a title or caption has been defined, set them now.
		if ( ! empty( $title ) || ! empty( $caption ) ) {
			$attachment = get_post( $image_id );
			wp_update_post(
				array(
					'ID'           => $image_id,
					'post_title'   => sanitize_text_field( $title ),
					'post_content' => sanitize_text_field( $description ),
					'post_excerpt' => sanitize_text_field( $caption ),
				)
			);
		}

		// If an alt tag has been specified, set it now.
		if ( ! empty( $alt_tag ) ) {
			update_post_meta( $image_id, '_wp_attachment_image_alt', $alt_tag );
		}

		// Return the image ID.
		return $image_id;

	}

	/**
	 * Deletes attachments that were added by an upload function in this class
	 * (i.e. that include the meta key flag)
	 *
	 * @since   4.2.0
	 */
	public function cleanup() {

		// Build query.
		$args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'   => '_' . $this->base->plugin->filter_name,
					'value' => 1,
				),
			),
			'fields'         => 'ids',
		);

		// Get all Attachments belonging to the given Post IDs.
		$attachments = new WP_Query( $args );

		// If no Attachments found, return false, as there's nothing to delete.
		if ( count( $attachments->posts ) === 0 ) {
			return false;
		}

		// Delete attachments.
		foreach ( $attachments->posts as $attachment_id ) {
			wp_delete_attachment( $attachment_id );
		}

		return true;

	}

	/**
	 * Flag to denote if the GD image processing library is available
	 *
	 * @since   4.2.0
	 *
	 * @return  bool    GD Library Available in PHP
	 */
	private function is_gd_available() {

		return extension_loaded( 'gd' ) && function_exists( 'gd_info' );

	}

	/**
	 * Force using the GD Image Library for processing WordPress Images.
	 *
	 * @since   4.2.0
	 *
	 * @param   array $editors    WordPress Image Editors.
	 * @return  array               WordPress Image Editors.
	 */
	public function enable_gd_image_support( $editors ) {

		$gd_editor = 'WP_Image_Editor_GD';
		$editors   = array_diff( $editors, array( $gd_editor ) );
		array_unshift( $editors, $gd_editor );
		return $editors;

	}

}
