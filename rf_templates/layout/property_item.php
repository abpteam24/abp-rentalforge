<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_property_item_template', function ( $abprf_infos, $property = [] ) {
		//echo '<pre>';print_r($property);echo '</pre>';
		if ( is_array( $property ) && sizeof( $property ) > 0 ) {
			$location    = array_key_exists( 'location', $abprf_infos ) ? $abprf_infos['location'] : '';
			$start_time  = array_key_exists( 'start_time', $abprf_infos ) ? $abprf_infos['start_time'] : '';
			$end_time    = array_key_exists( 'end_time', $abprf_infos ) ? $abprf_infos['end_time'] : '';
			$add_class   = array_key_exists( 'add_class', $abprf_infos ) ? $abprf_infos['add_class'] : '';
			$abprf_brand = ABPRF_Brands;
			$post_id     = array_key_exists( 'post_id', $property ) ? $property['post_id'] : '';
			$cat_id = ABPRF_Function::get_post_info( $post_id, 'abprf_category' );
			$loc_id = ABPRF_Function::get_post_info( $post_id, 'abprf_location' );
			$rent_rule      = array_key_exists( 'rent_rule', $property ) ? $property['rent_rule'] : '';
			$price_qty_info = array_key_exists( 'price_qty_info', $property ) ? $property['price_qty_info'] : '';
			$price_qty_info = ! empty( $price_qty_info ) ? json_decode( $price_qty_info, true ) : [];
			$price_qty_info = ! empty( $price_qty_info ) && ! empty( $location ) && array_key_exists( $location, $price_qty_info ) ? $price_qty_info[ $location ] : $price_qty_info;
			$price_info     = ! empty( $rent_rule ) && array_key_exists( $rent_rule, $price_qty_info ) ? $price_qty_info[ $rent_rule ] : [];
			$others         = array_key_exists( 'others', $property ) ? $property['others'] : '';
			$others         = ! empty( $others ) ? json_decode( $others, true ) : [];
			if ( ! empty( $rent_rule ) && ! empty( $price_info ) ) {
				$brand         = array_key_exists( 'brand', $property ) ? $property['brand'] : '';
				$time_duration = ABPRF_Function::time_duration( $abprf_infos, $price_info );
				$total_price   = ABPRF_Function::get_price( $abprf_infos, $property, $time_duration );
				$features      = array_key_exists( 'features', $property ) ? $property['features'] : '';
                $property_name=array_key_exists( 'name', $property ) ? $property['name'] : '';
				$abprf_infos['property_name']    = $property_name;
                $abprf_infos['property_id']      = array_key_exists( 'id', $property ) ? $property['id'] : '';
				//echo '<pre>';print_r($features);echo '</pre>';
				?>
                <div class="property_item item_box_1 <?php echo esc_attr( $add_class ); ?>" data-cat_id="<?php echo esc_attr( $cat_id ); ?>" data-loc_id="<?php echo esc_attr( $loc_id ); ?>">
                    <div class="item_head">
                        <div class="item_img _all_center">
							<?php ABPRF_Layout::image_icon( array_key_exists( 'icon', $others ) ? $others['icon'] : '' ); ?>
                        </div>
                        <h4 class="_abprf"><?php echo esc_html($property_name); ?></h4>
						<?php if ( ! empty( $brand ) ) { ?>
                            <p class="_abprf"><?php echo esc_html( array_key_exists( $brand, $abprf_brand ) && array_key_exists( 'name', $abprf_brand[ $brand ] ) ? $abprf_brand[ $brand ]['name'] : '' ); ?></p>
						<?php } ?>
                    </div>
                    <div class="item_body">
						<?php ABPRF_Layout::item_feature( $features ); ?>
                        <div class="pricing_box">
                            <div class="price_row">
								<?php ABPRF_Layout::item_price( $post_id, $rent_rule, $price_info ); ?>
                            </div>
                            <div class="item_condition">
								<?php echo esc_html( ABPRF_Layout::item_condition( $rent_rule, $price_info ) ); ?>
                            </div>
							<?php ABPRF_Layout::item_deposit( $price_info );
								if ( ! empty( $start_time ) && ! empty( $end_time ) && strtotime( $start_time ) < strtotime( $end_time ) ) {
									ABPRF_Layout::item_cost( $abprf_infos, $price_info, $total_price, $time_duration );
								}
							?>
                        </div>
						<?php
							if ( ! empty( $time_duration ) && ! empty( $start_time ) && ! empty( $end_time ) && strtotime( $start_time ) < strtotime( $end_time ) ) {
								ABPRF_Layout::item_select_property( $abprf_infos, $price_info, $total_price );
							}
							if ( ! empty( $add_class ) ) {
								?>
                                <div>
                                    <button type="button" class="_btn_theme_xs" data-href="<?php echo esc_url( get_the_permalink( $post_id ) ); ?>" data-blank="_blank">
										<?php esc_html_e( 'Book Now', 'abprf-rental-forge' ); ?>
                                    </button>
                                </div>
								<?php
							}
						?>
                    </div>
                </div>
				<?php
			}
		}
	}, 10, 2 );