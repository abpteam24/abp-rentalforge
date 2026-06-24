<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_gallery_template', function ( $img_infos = [], $abprf_slider = [] ) {
		if ( empty( $img_infos ) || ! is_array( $img_infos ) ) {
			return;
		}
		$popup_id     = uniqid( '#abprf_slider_' );
		$active_popup = $abprf_slider['visible_popup'] ?? 'on';
		$image_column = absint( $abprf_slider['image_column'] ?? 3 );
		$show_item    = $abprf_slider['show_item'] ?? '';
		$post_count   = 0;
		$image_column = ( $image_column > 0 && $image_column < 11 ) ? $image_column : 3;
		$total_images = count( $img_infos );
		?>
        <div class="_abp_panel">
            <div class="_panel_head">
                <h4 class="_abprf"><span class="far fa-image _mar_r_xxs"></span> <?php esc_html_e( 'Gallery', 'abp-rentalforge' ); ?></h4>
            </div>
            <div class="_panel_body_xs abprf_gallery">
                <div class="gallery_area item_<?php echo esc_attr( $image_column ); ?>">
					<?php
						foreach ( $img_infos as $img_info ) {
							if ( ! is_array( $img_info ) ) {
								continue;
							}
							$id = $img_info['id'] ?? '';
							if ( empty( $id ) ) {
								continue;
							}
							$add_class = '';
							if ( ! empty( $show_item ) ) {
								$add_class = ( absint( $show_item ) >= $post_count ) ? 'pagination_item' : 'pagination_item abp_close';
							}
							$post_count ++;
							$url       = ABPRF_Function::get_image_url( '', $id ) ?: ABPRF_BLANK_IMG_URL;
							$img_post  = $img_info['post'] ?? '';
							$img_label = $img_info['label'] ?? '';
							?>
                            <div class="gallery_item <?php echo esc_attr( $add_class ); ?>" data-img="<?php echo esc_url( $url ); ?>" <?php if ( $active_popup === 'on' ) { ?>data-target-popup="<?php echo esc_attr( $popup_id ); ?>"<?php } ?>>
                                <img src="#" alt="<?php echo esc_html( $img_post ); ?>"/>
                                <div class="item_caption">
                                    <div class="caption_label"><?php echo esc_html( $img_post ); ?></div>
                                    <div class="caption_title"><?php echo esc_html( $img_label ); ?></div>
                                </div>
                            </div>
						<?php } ?>
                </div>
				<?php
					if ( ! empty( $show_item ) ) {
						$args = [
							'total' => $total_images,
							'page_item' => $show_item,
						];
						do_action( 'abprf_pagination', $args );
					}
				?>
            </div>
        </div>
		<?php
		do_action( 'abprf_slider_popup', $abprf_slider, $img_infos, $popup_id );
	}, 10, 2 );