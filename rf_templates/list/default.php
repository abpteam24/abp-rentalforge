<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_default_template', function ( $params = [] ) {
		//echo '<pre>';print_r($params);echo '</pre>';
		$post_ids = $params['all_post'] ?? [];
		if ( ! empty( $post_ids ) && sizeof( $post_ids ) > 0 ) {
			$global_order      = $params['global_order'] ?? '';
			$style             = ( $params['style'] ?? 'grid' ) ?: 'grid';
			$column            = $params['column'] ?? 3;
			$related           = $params['related'] ?? '';
			$show_post         = absint( ( $params['show'] ?? 0 ) ?: ( $column * 3 ) );
			$class             = $style == 'grid' && $column > 1 ? 'abprf_grid item_' . $column : 'abprf_lists item_' . $column;
			$class             = ! empty( $related ) ? 'abprf_grid ' : $class;
			$post_count        = 0;
			$args['total']     = sizeof( $post_ids );
			$args['page_item'] = $show_post;
			asort( $post_ids );
			?>
            <div class="<?php echo esc_attr( $class ); ?>">
				<?php foreach ( $post_ids as $post_id ) {
					$post_count ++;
					$title       = get_the_title( $post_id );
					$features    = ABPRF_Function::get_post_info( $post_id, 'abprf_features' );
					$rent_rule   = ABPRF_Function::get_post_info( $post_id, 'rent_rule' );
					$cat_id      = ABPRF_Function::get_post_info( $post_id, 'abprf_category' );
					$loc_id      = ABPRF_Function::get_post_info( $post_id, 'abprf_location' );
					$description = ABPRF_Function::get_post_info( $post_id, 'post_description' );
					$url         = get_the_permalink( $post_id );
					$show_class  = $show_post >= $post_count ? '' : 'rf_close';
					//echo '<pre>';print_r($filter_args);echo '</pre>';
					?>
                    <div class="pagination_item item_box_1 <?php echo esc_attr( $show_class ); ?>" data-cat_id="<?php echo esc_attr( $cat_id ); ?>" data-loc_id="<?php echo esc_attr( $loc_id ); ?>">
                        <div class="item_head">
							<?php ABPRF_Layout::image( $post_id ); ?>
                        </div>
                        <div class="item_body">
                            <div>
                                <a class="_abprf list_title" href="<?php echo esc_url( $url ); ?>" target="_blank">
									<?php if ( ABPRF_Function::on_off( 'post_icon' ) ) {
										ABPRF_Layout::image_icon( ABPRF_Function::get_post_info( $post_id, 'post_icon' ) );
									}
										echo esc_html( $title ); ?>
                                </a>
								<?php do_action( 'abprf_location', $post_id );
									ABPRF_Layout::item_feature( $features );
									do_action( 'abprf_category', $post_id, 'ribbon' );
									if ( ABPRF_Function::on_off( 'post_des' ) && $description ) { ?>
                                        <div class="item_condition"><?php echo esc_html( $description ); ?></div>
									<?php } ?>
                            </div>
                            <div>
                                <div class="_divider_xxs"></div>
                                <div class="_fj_between">
									<?php if ( ! empty( $rent_rule ) ) {
										$min_rate = ABPRF_Min_Price[ $post_id ] ?? 0;
										$min_rate = $min_rate > 0 ? ABPRF_Function::tax_with_price( $post_id, $min_rate ) : 0;
										?>
                                        <span class="price_value">
                                                <?php
	                                                esc_html_e( 'Min Rent :', 'abp-rentalforge' );
	                                                echo $min_rate > 0 ? wp_kses_post( wc_price( $min_rate ) ) : esc_html__( 'Free', 'abp-rentalforge' );
	                                                echo esc_html( ABPRF_Layout::per_rent_rules( $rent_rule ) );
                                                ?>
                                            </span>
									<?php } ?>
									<?php if ( ! empty( $global_order ) ) { ?>
                                        <button type="button" class="_btn_theme_xs select_post" data-post_id="<?php echo esc_attr( $post_id ); ?>">
											<?php esc_html_e( 'Book Now', 'abp-rentalforge' ); ?>
                                        </button>
									<?php } else { ?>
                                        <button type="button" class="_btn_theme_xs" data-href="<?php echo esc_url( $url ); ?>" data-blank="_blank">
											<?php esc_html_e( 'Book Now', 'abp-rentalforge' ); ?>
                                        </button>
									<?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>
			<?php if ( empty( $related ) ) {
				do_action( 'abprf_pagination', $args );
			} ?>
			<?php
		} else {
			ABPRF_Layout::layout_warning_info( 'not_found' );
		}
	}, 10, 2 );