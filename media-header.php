<?php
	/*
	Plugin Name: Media Header
	Plugin URI:
	Description: Provides several types of page headers, including a static hero image, an image slideshow, an HTML5 video, and a Youtube embedded video.
	Version:     1.0
	Author:      Brian Blosser
	Author URI:  https://github.com/bpb54321
	License:     GPL2
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
	*/

	//Run when the plugin is activated or deactivated or uninstalled
	register_activation_hook( __FILE__, 'mh_activate' );
	function mh_activate() {

	}
	register_deactivation_hook( __FILE__, 'mh_dactivate' );
	function mh_deactivate() {

	}

	register_uninstall_hook( __FILE__, 'mh_uninstall' );
	function mh_uninstall() {

	}

	//--------------------------------------Enqueue Javascript and Styles----------------------------------//
	add_action( 'wp_enqueue_scripts', 'mh_enqueue_scripts_and_styles');
	function mh_enqueue_scripts_and_styles() {
        wp_enqueue_script( 'mh-scripts-js', plugins_url( 'js/mh-scripts.js', __FILE__ ), ['jquery', 'slick-js'], null, true );
        wp_enqueue_style( 'mh-styles-css', plugins_url( 'scss/style.css', __FILE__ ), [], null, 'all' );
	}


	//-----------------------Register metaboxes relevant to this plugin using CMB2---------------//
	/**
	 * Include and setup custom metaboxes and fields. (make sure you copy this file to outside the CMB2 directory)
	 *
	 * Be sure to replace all instances of 'cmb2_' with your project's prefix.
	 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
	 *
	 * @category YourThemeOrPlugin
	 * @package  Demo_CMB2
	 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
	 * @link     https://github.com/WebDevStudios/CMB2
	 */
	/**
	 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
	 */

	if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
		require_once dirname( __FILE__ ) . '/cmb2/init.php';
	} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
		require_once dirname( __FILE__ ) . '/CMB2/init.php';
	}

	add_action( 'cmb2_admin_init', 'mh_register_page_metaboxes' );
	function mh_register_page_metaboxes() {
		$prefix = 'header_video_'; //For historical reasons
		$header_video_metabox = new_cmb2_box( array(
			'id'            => $prefix . 'metabox',
			'title'         => 'Media Header Page Options',
			'object_types'  => array( 'page', ), // Post type
			// 'show_on_cb' => 'cmb2_show_if_front_page', // function should return a bool value
			// 'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // true to keep the metabox closed by default
		) );

		$header_video_metabox->add_field( array(
			'name'             => __( 'Media Type', 'cmb2' ),
			'desc'             => __( 'Select which media type will be displayed as the page header.', 'cmb2' ),
			'id'               => $prefix . 'media_type',
			'type'             => 'radio_inline',
			'show_option_none' => false,
			'default'		   => 'image',
			'options'          => array(
				'image' => __( 'Featured Image', 'cmb2' ),
				'video'   => __( 'Uploaded Video', 'cmb2' ),
				'youtube' => __( 'Youtube Video', 'cmb2' ),
				'slideshow' => __( 'Image Slideshow', 'cmb2' ),
			),
		) );

		$header_video_metabox->add_field( array(
			'name'         => __( 'Poster Image', 'cmb2' ),
			'desc'         => __( 'Only used if media type is set to Uploaded Video. The poster image specifies an image to be shown while the video is downloading, or until the user hits the play button. If this is not included, the first frame of the video will be used instead.', 'cmb2' ),
			'id'           => $prefix . 'poster_img',
			'type'         => 'file',
			'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
		) );

		$header_video_metabox->add_field( array(
			'name' => __( 'MP4 Video File', 'cmb2' ),
			'desc' => __( 'Select a video in MP4 format. Only used if media type is set to Uploaded Video.', 'cmb2' ),
			'id'   => $prefix . 'mp4_file',
			'type' => 'file',
		) );

		$header_video_metabox->add_field( array(
			'name' => __( 'Webm Video File', 'cmb2' ),
			'desc' => __( 'Select a video in Webm format. Only used if media type is set to Uploaded Video.', 'cmb2' ),
			'id'   => $prefix . 'webm_file',
			'type' => 'file',
		) );

		$header_video_metabox->add_field( array(
			'name' => __( 'OGG Video File', 'cmb2' ),
			'desc' => __( 'Select a video in OGG format. Only used if media type is set to Uploaded Video.', 'cmb2' ),
			'id'   => $prefix . 'ogg_file',
			'type' => 'file',
		) );

		$header_video_metabox->add_field( array(
			'name' => __( 'Youtube Video ID', 'cmb2' ),
			'desc' => __( 'Unique ID for the Youtube video to embed.', 'cmb2' ),
			'id'   => $prefix . 'youtube_id',
			'type' => 'text_medium',
		) );

		$header_video_metabox->add_field( array(
			'name'         => __( 'Slideshow Images', 'cmb2' ),
			'desc'         => __( 'Select images to be shown in the slideshow.', 'cmb2' ),
			'id'           => $prefix . 'slideshow_image_ids',
			'type'         => 'file_list',
			'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
		) );
	}

    //-----------------------Display the media header by hooking into the 'mh_display_media_header' hook that theme must provide-----------//
	include_once( 'includes/media-header-view.php' );


    //--------------------------Helper functions---------------------------------//
	/**Helper function for getting the image source of a post's featured image
     * @param: Int $post_id: The post id of the post that you are getting the featured image for
     * @param: String $size: The Wordpress keyword for the image size (thumbnail, medium, large, full);
     * @return: String The URL of the featured image.
     */
	function mh_get_featured_image_src($post_id, $size) {
		$post_thumbnail_id = get_post_thumbnail_id ( $post_id );
		$image_src_array = wp_get_attachment_image_src ( $post_thumbnail_id, $size, false );

		$image_src_string = $image_src_array[0];

		return $image_src_string;
	}
    /**
     * Helper function for getting the widths and url's of all the available crops of an image.
     * @param  Number $id: The id of the image that you need the info for
     * @param  Array $image_sizes_to_exclude An array of strings with the names of image sizes to exclude from the output
     * @return Array An indexed array, where at each index there is an associative array with array["width"] containing an image width and array["url"] containing the corresponding image url.
     *          Example: Array(
     *                       [0] => Array(
     *                               [width] => 300
     *                               [url] => http://placehold.it/50x50
     *                          ),
     *                      [1] , etc
     */
    function mh_get_image_widths_and_urls($id, $image_sizes_to_exclude = [] ) {
        //Get all the possible image sizes
        $image_size_array = get_intermediate_image_sizes();

        //Filter out sizes we don't want to include
        foreach ( $image_sizes_to_exclude as $image_size_to_exclude ) {
            $size_found_key = array_search( $image_size_to_exclude, $image_size_array );
            if ( $size_found_key !== false ) {
                unset( $image_size_array[ $size_found_key ] );
            }
        }

        $image_width_array = [];
        $widths_and_urls = [];
        $i = 0;
        foreach ( $image_size_array as $image_size_name ) {
            $image_info_array = wp_get_attachment_image_src($id, $image_size_name);
            $is_intermediate_image_size = $image_info_array[3]; //False if it's the original image, not a scaled version
            if ( $is_intermediate_image_size ) {
                $image_url = $image_info_array[0];
                $image_width = $image_info_array[1];
                $image_width_array[] = $image_width;
                $widths_and_urls[] = [
                    "width" => $image_width,
                    "url" => $image_url,
                ];
            }
            $i++;
        }
        //Get the full size image
        $full_size_image_info_array = wp_get_attachment_image_src($id, "full");
        $image_url = $full_size_image_info_array[0];
        $image_width = $full_size_image_info_array[1];
        $image_width_array[] = $image_width;
        $widths_and_urls[] = [
            "width" => $image_width,
            "url" => $image_url,
        ];

        array_multisort ($image_width_array, SORT_ASC, SORT_NUMERIC, $widths_and_urls); //Sorts $widths_and_urls by the "width" key

        return $widths_and_urls;
    }
