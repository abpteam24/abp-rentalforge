<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_slider_template', function ( $img_infos = [], $abprf_slider = [] ) {
		if ( ! empty( $img_infos ) && sizeof( $img_infos ) > 0 ) {
			$slider_indicator    = isset( $abprf_slider['indicator_visible'] ) && $abprf_slider['indicator_visible'] ? $abprf_slider['indicator_visible'] : 'on';
			$indication_position = array_key_exists( 'indication_position', $abprf_slider ) && $abprf_slider['indication_position'] ? $abprf_slider['indication_position'] : 'bottom';
			$indication_position = $slider_indicator == 'on' ? $indication_position : '';
			$popup_id            = uniqid( '#abprf_slider_' );
			$active_popup        = isset( $abprf_slider['visible_popup'] ) && $abprf_slider['visible_popup'] ? $abprf_slider['visible_popup'] : 'on';
			?>
            <div class="abprf_slider">
                <div class="<?php echo esc_attr( $indication_position ); ?>" data-rf-slider>
                    <div class="slider_show">
                        <div class="_circle_icon slide_counter"><span class="slide_current_num">1</span> / <span><?php echo esc_html( sizeof( $img_infos ) ); ?></span></div>
                        <img src="#" class="slide_resize" alt="" aria-hidden="true"/>
						<?php foreach ( $img_infos as $img_info ) {
							$id = array_key_exists( 'id', $img_info ) ? $img_info['id'] : '';
							if ( ! empty( $id ) ) {
								$url = ABPRF_Function::get_image_url( '', $id );
								$url = $url ?: ABPRF_BLANK_IMG_URL;
								?>
                                <div class="slider_item" <?php if ( $active_popup == 'on' ){ ?>data-target-popup="<?php echo esc_attr( $popup_id ); ?>"<?php } ?> data-img="<?php echo esc_url( $url ); ?>">
                                    <div class="slider_loading"></div>
                                    <img src="#" alt="<?php echo esc_html( array_key_exists( 'post', $img_info ) ? $img_info['post'] : '' ); ?>"/>
                                    <div class="item_caption">
                                        <div class="caption_label"><?php echo esc_html( array_key_exists( 'post', $img_info ) ? $img_info['post'] : '' ); ?></div>
                                        <div class="caption_title"><?php echo esc_html( array_key_exists( 'label', $img_info ) ? $img_info['label'] : '' ); ?></div>
                                    </div>
                                </div>
							<?php }
						} ?>
                    </div>
                    <div class="progress_bar">
                        <div class="progress_fill"></div>
                    </div>
                    <div class="icon_direction prev_item">
                        <span class="fas fa-chevron-left"></span>
                    </div>
                    <div class="icon_direction next_item">
                        <span class="fas fa-chevron-right"></span>
                    </div>
					<?php if ( $slider_indicator == 'on' ) { ?>
                        <div class="image_indicator"></div>
					<?php } ?>
                </div>
            </div>
			<?php
			do_action( 'abprf_slider_popup', $abprf_slider, $img_infos, $popup_id );
		}
	}, 10, 3 );
