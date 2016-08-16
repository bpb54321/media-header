<?php
    //This file is included from media-header.php.
    add_action( 'mh_display_media_header', 'mh_display_media_header', 10, 1);
    /**
     * Renders the media header.
     * @param $post_id The post id of the post that is rendering.
     */
    function mh_display_media_header( $post_id )
    {
        $header_video_media_type = get_post_meta($post_id, 'header_video_media_type', true);

        if ($header_video_media_type == 'image' || $header_video_media_type == '') {
            $post_thumbnail_id = get_post_thumbnail_id ( $post_id );

            $widths_and_urls = mh_get_image_widths_and_urls( $post_thumbnail_id );

            $json_widths_and_urls = json_encode($widths_and_urls);

            echo "<div class='hero-image' data-image-data='{$json_widths_and_urls}'></div>";
        } elseif ($header_video_media_type == 'slideshow') {
            $image_ids_container_array = get_post_meta($post_id, 'header_video_slideshow_image_ids', false);

            if (sizeof($image_ids_container_array) > 0) {
                $slideshow_image_ids = $image_ids_container_array[0];
                echo "<section class='hero-image-slideshow'>";
                //Insert the slideshow images
                foreach ($slideshow_image_ids as $slideshow_image_id => $slideshow_image_full_url) {

                    $widths_and_urls = mh_get_image_widths_and_urls($slideshow_image_id, ['thumbnail']);
                    $json_widths_and_urls = json_encode($widths_and_urls);

                    echo "<div class='hero-image-slide' data-image-data='{$json_widths_and_urls}'></div>";
                }
                echo "</section>";
            } else {
                //Print an empty section with a colored background.
                echo "<section class='hero-image-slideshow image-slideshow-replacement' >";
                echo "</section>";
            }
        } elseif ($header_video_media_type == 'video') {

            //--------------------------Video Header----------------------------//
            $header_video_mp4_file_url = get_post_meta($post_id, 'header_video_mp4_file', true);
            $header_video_webm_file_url = get_post_meta($post_id, 'header_video_webm_file', true);
            $header_video_ogg_file_url = get_post_meta($post_id, 'header_video_ogg_file', true);
            $poster_image_id = get_post_meta($post_id, 'header_video_poster_img_id', true);

            if ( $poster_image_id == 0 ) { //No poster image found
                //Use featured image as backup
                $image_src_string = mh_get_featured_image_src($post_id, 'large'); //Defined in functions.php
                if ( isset($image_src_string) ) {
                    $poster_image_src = $image_src_string;
                }
                else {
                    $poster_image_src = '';
                }
            } else {
                $poster_image_src_array = wp_get_attachment_image_src($poster_image_id, 'large');
                $poster_image_src = $poster_image_src_array[0];
            }

            $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

            if (stripos($user_agent, 'android') !== false) {
                $is_android_browser = true;
            } else {
                $is_android_browser = false;
            }
            if ($is_android_browser) { /*-------------------Android Section--------------*/
                ?>
                <section class="hero-image" style="background-image: url('<?php echo $poster_image_src; ?>')">
                    <svg id="video-play-overlay-button" width="100" height="100">
                        <rect x="0" y="0" width="100" height="100" rx="10px" ry="10px"/>
                        <polygon points="25 20, 80 50, 25 80"/>
                    </svg>
                </section>
                <div id="video-container-android" style="display:none;">
                    <video id="video-background" preload="auto" controls>
                        <?php if ($header_video_mp4_file_url !== '') : ?>
                            <source src="<?php echo $header_video_mp4_file_url; ?>" type="video/mp4">
                        <?php endif; ?>
                        <?php if ($header_video_webm_file_url !== '') : ?>
                            <source src="<?php echo $header_video_webm_file_url; ?>" type="video/webm">
                        <?php endif; ?>
                        <?php if ($header_video_ogg_file_url !== '') : ?>
                            <source src="<?php echo $header_video_ogg_file_url; ?>" type="video/ogg">
                        <?php endif; ?>
                    </video>
                </div>
                <?php
            }
            else { /*-------------------All Others Section---------------------------*/ ?>
                <div id="video-container">
                    <video id="video-background" preload="auto" autoplay="true" loop="loop" muted="muted" volume="0"
                           poster="<?php echo $poster_image_src; ?>" width="100%" height="100%">
                        <?php if ($header_video_mp4_file_url !== '') : ?>
                            <source src="<?php echo $header_video_mp4_file_url; ?>" type="video/mp4">
                        <?php endif; ?>
                        <?php if ($header_video_webm_file_url !== '') : ?>
                            <source src="<?php echo $header_video_webm_file_url; ?>" type="video/webm">
                        <?php endif; ?>
                        <?php if ($header_video_ogg_file_url !== '') : ?>
                            <source src="<?php echo $header_video_ogg_file_url; ?>" type="video/ogg">
                        <?php endif; ?>
                    </video>
                </div>
                <?php
            }
        } elseif ($header_video_media_type == 'youtube') {
            $header_video_youtube_id = get_post_meta($post_id, 'header_video_youtube_id', true);
            ?>
            <div id='youtube-header-container' data-youtube-id="<?php echo $header_video_youtube_id; ?>">
            </div>
            <?php
        }
    }

