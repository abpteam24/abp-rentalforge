<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_gallery_template', function ( $img_infos = [], $abprf_slider = [] ) {
		if ( ! empty( $img_infos ) && sizeof( $img_infos ) > 0 ) {
			$popup_id     = uniqid( '#abprf_slider_' );
			$active_popup = $abprf_slider['visible_popup'] ?? 'on';
			$image_column = $abprf_slider['image_column'] ?? 3;
			$show_item    = $abprf_slider['show_item'] ?? '';
			$add_class    = '';
			$post_count   = 0;
			$image_column = ( $image_column > 0 && $image_column < 11 ) ? $image_column : 3;
			?>
            <div class="_abp_panel ">
                <div class="_panel_head">
                    <h4 class="_abprf"><span class="far fa-image _mar_r_xxs"></span> <?php esc_html_e( 'Gallery', 'abp-rentalforge' ); ?></h4>
                </div>
                <div class="_panel_body_xs abprf_gallery">
                    <div class="gallery_area item_<?php echo esc_attr( $image_column ); ?>">
						<?php foreach ( $img_infos as $img_info ) {
							$id = is_array( $img_info ) && array_key_exists( 'id', $img_info ) ? $img_info['id'] : '';
							if ( ! empty( $id ) ) {
								if ( ! empty( $show_item ) ) {
									$add_class = $show_item >= $post_count ? 'pagination_item' : 'pagination_item rf_close';
								}
								$post_count ++;
								$url = ABPRF_Function::get_image_url( '', $id );
								$url = $url ?: ABPRF_BLANK_IMG_URL;
								?>
                                <div class="gallery_item <?php echo esc_attr( $add_class ); ?>" data-img="<?php echo esc_url( $url ); ?>" <?php if ( $active_popup == 'on' ){ ?>data-target-popup="<?php echo esc_attr( $popup_id ); ?>"<?php } ?>>
                                    <img src="#" alt="<?php echo esc_html( array_key_exists( 'post', $img_info ) ? $img_info['post'] : '' ); ?>"/>
                                    <div class="item_caption">
                                        <div class="caption_label"><?php echo esc_html( $img_info['post'] ?? '' ); ?></div>
                                        <div class="caption_title"><?php echo esc_html( $img_info['label'] ?? '' ); ?></div>
                                    </div>
                                </div>
							<?php }
						} ?>
                    </div>
					<?php
						if ( ! empty( $show_item ) ) {
							$args['total']     = sizeof( $img_infos );
							$args['page_item'] = $show_item;
							do_action( 'abprf_pagination', $args );
						}
					?>
                </div>
            </div>
			<?php
			do_action( 'abprf_slider_popup', $abprf_slider, $img_infos, $popup_id );
		}
	}, 10, 3 );
