<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_category_template', function ( $post_id, $ribbon = '' ) {
		$display_category = ABPRF_Function::get_post_info( $post_id, 'display_category', 'on' );
		$cat_id           = ABPRF_Function::get_post_info( $post_id, 'abprf_category' );
		$category         = is_array( ABPRF_Category ) && array_key_exists( $cat_id, ABPRF_Category ) ? ABPRF_Category[ $cat_id ]['name'] : '';
		if ( $category && $display_category == 'on' ) {
			if ( $ribbon == 'ribbon' ) { ?>
                <div class="ribbon publish"><?php echo esc_html( $category ); ?></div>
			<?php } else { ?>
                <div class="_fs_label">
					<?php echo esc_html( ABPRF_Function::category_label() . ' :  ' . $category ); ?>
                </div>
				<?php
			}
		}
	}, 10, 3 );
