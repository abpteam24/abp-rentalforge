<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_title_template', function ( $post_id, $abprf_infos = [] ) {
		if ( $post_id > 0 ) {
			$display_sku = array_key_exists( 'display_sku', $abprf_infos ) ? $abprf_infos['display_sku'] : 'off';
			$post_sku    = array_key_exists( 'post_sku', $abprf_infos ) ? $abprf_infos['post_sku'] : '';
			$abprf_configuration    = array_key_exists( 'abprf_configuration', $abprf_infos ) ? $abprf_infos['abprf_configuration'] : ABPRF_Function::get_option( 'abprf_configuration' );
			$brand_icon = isset( $abprf_configuration['brand_icon'] ) && $abprf_configuration['brand_icon'] ? $abprf_configuration['brand_icon'] : '';
			?>
            <h1 class="_abprf_color_theme">
				<?php ABPRF_Layout::image_icon( $brand_icon,'_mar_r_xs' ); ?>
				<?php echo esc_html( get_the_title( $post_id ) ); ?>
				<?php if ( $post_sku && $display_sku == 'on' ) { ?>
                    <small class="_abprf_color_gray">&nbsp;(<?php echo esc_html( $post_sku ); ?>)</small>
				<?php } ?>
            </h1>
			<?php
		}
	}, 10, 2 );

