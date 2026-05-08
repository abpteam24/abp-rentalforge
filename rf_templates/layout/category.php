<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_category_template', function ( $post_id, $all_categories = [], $ribbon = '' ) {
		$all_categories   = ! empty( $all_categories ) && sizeof( $all_categories ) > 0 ? $all_categories : ABPRF_Function::get_option( 'abprf_category' );
		$display_category = ABPRF_Function::get_post_info( $post_id, 'display_category', 'on' );
		$cat_id           = ABPRF_Function::get_post_info( $post_id, 'category' );
		$category         = is_array( $all_categories ) && array_key_exists( $cat_id, $all_categories ) ? $all_categories[ $cat_id ]['name'] : '';
		if ( $category && $display_category == 'on' ) {
			if ( $ribbon == 'ribbon' ) { ?>
                <div class="ribbon "><?php echo esc_html( $category ); ?></div>
			<?php } else { ?>
                <span class="_fs_label_mar_r">
                    <?php
                        $abprf_configuration = ABPRF_Function::get_option( 'abprf_configuration' );
                        $category_label      = is_array( $abprf_configuration ) && array_key_exists( 'category_label', $abprf_configuration ) && $abprf_configuration['category_label'] ? $abprf_configuration['category_label'] : __( 'Category : ', 'abprf-rental-forge' );
                        echo esc_html( $category_label . ' :  ' . $category );
                    ?>
              </span>
				<?php
			}
		}
	}, 10, 3);
