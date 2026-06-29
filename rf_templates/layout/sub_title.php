<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_sub_title_template', function ( $post_id, $abprf_infos = [] ) {
		if ( ABPRF_Function::on_off( 'sub_title' ) ) {
			$post_id = absint( $post_id );
			if ( $post_id <= 0 ) {
				return;
			}
			$display_sub_title = $abprf_infos['display_sub_title'] ?? ABPRF_Function::get_post_info( $post_id, 'display_sub_title', 'off' );
			$sub_title         = $abprf_infos['sub_title'] ?? ABPRF_Function::get_post_info( $post_id, 'sub_title' );
			if ( $display_sub_title === 'on' && $sub_title !== '' ) {
				?>
                <p class="_abp sub_title"><?php echo esc_html( $sub_title ); ?></p>
				<?php
			}
		}
	}, 10, 2 );