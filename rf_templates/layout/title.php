<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_title_template', function ( $post_id, $abprf_infos = [] ) {
		if (!empty($post_id) &&  $post_id > 0 ) {
			$display_sku =  $abprf_infos['display_sku'] ??ABPRF_Function::get_post_info($post_id,'display_sku','off');
			$post_sku    = $abprf_infos['post_sku']?? ABPRF_Function::get_post_info($post_id,'post_sku');
			?>
            <h1 class="_abprf_color_theme">
				<?php echo esc_html( get_the_title( $post_id ) ); ?>
				<?php if ( !empty($post_sku) && $display_sku == 'on' ) { ?>
                    <small class="_abprf_color_gray">&nbsp;(<?php echo esc_html( $post_sku ); ?>)</small>
				<?php } ?>
            </h1>
			<?php
		}
	}, 10, 2 );

