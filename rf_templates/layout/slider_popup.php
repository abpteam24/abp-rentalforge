<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_slider_popup_template', function ( $abprf_slider, $img_infos, $popup_id = '' ) {
		$active_popup = isset( $abprf_slider['visible_popup'] ) && $abprf_slider['visible_popup'] ? $abprf_slider['visible_popup'] : 'on';
		$indicator    = isset( $abprf_slider['popup_image_indicator'] ) && $abprf_slider['popup_image_indicator'] ? $abprf_slider['popup_image_indicator'] : 'on';
		if ( $active_popup == 'on' ) { ?>
            <div class="abprf_popup" data-popup="<?php echo esc_attr( $popup_id ); ?>">
                <div class="popup_area abprf_slider">
                    <span class="popup_close"><i class="fas fa-times"></i></span>
                    <div data-rf-slider>
                        <div class="popup_body">
                            <div class="slider_show">
                                <div class="_circle_icon slide_counter"><span class="slide_current_num">1</span> / <span><?php echo esc_html( sizeof( $img_infos ) ); ?></span></div>
                                <img src="#" class="slide_resize" alt="" aria-hidden="true"/>
								<?php foreach ( $img_infos as $img_info ) {
									$id = is_array($img_info) && array_key_exists( 'id', $img_info ) ? $img_info['id'] : '';
									if ( ! empty( $id ) ) {
										$url = ABPRF_Function::get_image_url( '', $id );
										$url = $url ?: ABPRF_BLANK_IMG_URL;
										?>
                                        <div class="slider_item" data-img="<?php echo esc_url( $url ); ?>">
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
                        </div>
						<?php if ( $indicator == 'on' ) { ?>
                            <div class="popup_foot">
                                <div class="image_indicator"></div>
                            </div>
						<?php } ?>
                    </div>
                </div>
            </div>
			<?php
		}
	}, 10, 3 );
