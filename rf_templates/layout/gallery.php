<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_gallery_template', function ( $img_infos = [], $abprf_slider = []) {
		if ( ! empty( $img_infos ) && sizeof( $img_infos ) > 0 ) {
			$popup_id     = uniqid( '#abprf_slider_' );
			$active_popup = isset( $abprf_slider['visible_popup'] ) && $abprf_slider['visible_popup'] ? $abprf_slider['visible_popup'] : 'on';
			$image_column = isset( $abprf_slider['image_column'] ) && $abprf_slider['image_column'] ? $abprf_slider['image_column'] : 3;
			?>
            <div class="_abp_panel ">
                <div class="_panel_head">
                    <h4 class="_abprf"><span class="far fa-image _mar_r_xxs"></span> <?php esc_html_e( 'Gallery', 'abprf-rental-forge' ); ?></h4>
                </div>
                <div class="_panel_body_xs abprf_gallery">
                    <div class="gallery_area item_<?php echo esc_attr($image_column);?>">
						<?php foreach ( $img_infos as $img_info ) {
							$id = is_array($img_info) && array_key_exists( 'id', $img_info ) ? $img_info['id'] : '';
							if ( ! empty( $id ) ) {
								$url = ABPRF_Function::get_image_url( '', $id );
								$url = $url ?: ABPRF_BLANK_IMG_URL;
								?>
                                <div class="gallery_item " data-img="<?php echo esc_url( $url ); ?>" <?php if ( $active_popup == 'on' ){ ?>data-target-popup="<?php echo esc_attr( $popup_id ); ?>"<?php } ?>>
                                    <img src="#" alt="<?php echo esc_html( array_key_exists( 'post', $img_info ) ? $img_info['post'] : '' ); ?>"/>
                                    <div class="item_caption">
                                        <div class="caption_label"><?php echo esc_html( array_key_exists( 'post', $img_info ) ? $img_info['post'] : '' ); ?></div>
                                        <div class="caption_title"><?php echo esc_html( array_key_exists( 'label', $img_info ) ? $img_info['label'] : '' ); ?></div>
                                    </div>
                                </div>
							<?php }
						} ?>
                    </div>
                </div>
            </div>
			<?php
			do_action( 'abprf_slider_popup', $abprf_slider, $img_infos, $popup_id );
		}
	}, 10, 3 );
