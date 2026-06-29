<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_title_template', function ( $post_id, $abprf_infos = [] ) {
		if ( ! empty( $post_id ) && $post_id > 0 ) {
			$display_sku = $abprf_infos['display_sku'] ?? ABPRF_Function::get_post_info( $post_id, 'display_sku', 'off' );
			$post_sku    = $abprf_infos['post_sku'] ?? ABPRF_Function::get_post_info( $post_id, 'post_sku' );
			?>
            <h1 class="_abp_color_theme">
				<?php
					if ( ABPRF_Function::on_off( 'post_icon' ) ) {
						ABPRF_Layout::image_icon( ( $abprf_infos['post_icon'] ?? ABPRF_Function::get_post_info( $post_id, 'post_icon' ) ) );
					}
					echo esc_html( get_the_title( $post_id ) ); ?>
				<?php if ( ! empty( $post_sku ) && $display_sku == 'on' && ABPRF_Function::on_off( 'sku' ) ) { ?>
                    <small class="_abp_color_gray">&nbsp;(<?php echo esc_html( $post_sku ); ?>)</small>
				<?php } ?>
            </h1>
			<?php
		}
	}, 10, 2 );

