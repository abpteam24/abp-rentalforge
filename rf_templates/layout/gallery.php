<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_gallery_template', function ( $abprf_slider = [], $post_id = '' ) {
		$img_infos = ABPRF_Function::get_slider_info( $post_id );
		if ( ! empty( $img_infos ) && sizeof( $img_infos ) > 0 ) {
			$popup_id     = uniqid( '#abprf_slider_' );
			$active_popup = isset( $abprf_slider['visible_popup'] ) && $abprf_slider['visible_popup'] ? $abprf_slider['visible_popup'] : 'on';
			?>
            <div class="_abp_panel ">
                <div class="_panel_head">
                    <h4 class="_abprf"><span class="far fa-image _mar_r_xxs"></span> <?php esc_html_e( 'Gallery', 'abprf-rental-forge' ); ?></h4>
                </div>
                <div class="_panel_body_xs abprf_gallery">
                    <div class="gallery_area">
						<?php foreach ( $img_infos as $img_info ) { ?>
                            <div class="gallery_item" data-img="<?php echo esc_url( array_key_exists( 'url', $img_info ) ? $img_info['url'] : '' ); ?>" <?php if ( $active_popup == 'on' ){ ?>data-target-popup="<?php echo esc_attr( $popup_id ); ?>"<?php } ?>>
                                <img src="#" alt="<?php echo esc_html( array_key_exists( 'post', $img_info ) ? $img_info['post'] : '' ); ?>"/>
                                <div class="item_caption">
                                    <div class="caption_label"><?php echo esc_html( array_key_exists( 'post', $img_info ) ? $img_info['post'] : '' ); ?></div>
                                    <div class="caption_title"><?php echo esc_html( array_key_exists( 'label', $img_info ) ? $img_info['label'] : '' ); ?></div>
                                </div>
                            </div>
						<?php } ?>
                    </div>
                </div>
            </div>
			<?php
			do_action( 'abprf_slider_popup', $abprf_slider, $img_infos, $popup_id );
		}
	}, 10, 3 );
