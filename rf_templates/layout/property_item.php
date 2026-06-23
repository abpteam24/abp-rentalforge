<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_property_item_template', function ( $abprf_infos, $property = [] ) {
		//echo '<pre>';print_r($property);echo '</pre>';
		if ( is_array( $property ) && sizeof( $property ) > 0 ) {
			$location       = $abprf_infos['location'] ?? '';
			$start_time     = $abprf_infos['start_time'] ?? '';
			$end_time       = $abprf_infos['end_time'] ?? '';
			$post_id        = $property['post_id'] ?? '';
			$cat_id         = ABPRF_Function::get_post_info( $post_id, 'abprf_category' );
			$loc_id         = ABPRF_Function::get_post_info( $post_id, 'abprf_location' );
			$rent_rule      = $property['rent_rule'] ?? '';
			$price_qty_info = json_decode( $property['price_qty_info'] ?? '', true ) ?: [];
			if ( ! empty( $location ) && isset( $price_qty_info[ $location ] ) ) {
				$price_qty_info = $price_qty_info[ $location ];
			}
			$price_info = $price_qty_info[ $rent_rule ] ?? [];
			$others     = json_decode( $property['others'] ?? '', true ) ?: [];
			if ( ! empty( $rent_rule ) && ! empty( $price_info ) ) {
				$time_duration                = ABPRF_Function::time_duration( $abprf_infos, $price_info );
				$total_price                  = ABPRF_Function::get_price( $abprf_infos, $property, $time_duration );
				$property_name                = $property['name'] ?? '';
				$abprf_infos['property_name'] = $property_name;
				$abprf_infos['property_id']   = $property['id'] ?? '';
				//echo '<pre>';print_r($property );echo '</pre>';
				?>
                <div class="property_item item_box_1 <?php echo esc_attr( $abprf_infos['add_class'] ?? '' ); ?>" data-cat_id="<?php echo esc_attr( $cat_id ); ?>" data-loc_id="<?php echo esc_attr( $loc_id ); ?>">
                    <div class="item_head">
						<?php ABPRF_Layout::image_icon( ($others['icon'] ?? ''),'' ); ?>
                    </div>
                    <div class="item_body">
                        <div>
                            <h5 class="_abprf list_title">
								<?php echo esc_html( $property_name ); ?>
								<?php if ( ($property['brand'] ?? '') && ABPRF_Function::on_off( 'brand' )  ) { ?>
                                    <small class="_abprf_color_theme"><?php echo esc_html( ABPRF_Function::brand_value( $property['brand'] ?? '' ) ); ?></small>
								<?php } ?>
                            </h5>
							<?php ABPRF_Layout::item_feature( $property['features'] ?? '' ); ?>
                            <div class="pricing_box">
                                <div class="price_row">
									<?php ABPRF_Layout::item_price( $post_id, $rent_rule, $price_info ); ?>
                                </div>
                                <div class="item_condition">
									<?php echo esc_html( ABPRF_Layout::item_condition( $rent_rule, $price_info ) ); ?>
                                </div>
								<?php ABPRF_Layout::item_deposit( $price_info );
									if ( ABPRF_Function::on_off( 'property_des' ) && ($others['description'] ?? '') ) { ?>
                                        <div class="item_condition"><?php echo esc_html( $others['description'] ?? '' ); ?></div>
									<?php }
									if ( ! empty( $start_time ) && ! empty( $end_time ) ) {
										ABPRF_Layout::item_cost( $abprf_infos, $price_info, $total_price, $time_duration );
									}
								?>
                            </div>
                        </div>
						<?php
							if ( ! empty( $time_duration ) && ! empty( $start_time ) && ! empty( $end_time ) ) {
								ABPRF_Layout::item_select_property( $abprf_infos, $price_info, $total_price );
							}
							if ( $abprf_infos['add_class'] ?? '' ) { ?>
                                <div>
                                    <div class="_divider_xs"></div>
                                    <button type="button" class="_btn_theme_xs" data-href="<?php echo esc_url( get_the_permalink( $post_id ) ); ?>" data-blank="_blank">
										<?php esc_html_e( 'Book Now', 'abp-rentalforge' ); ?>
                                    </button>
                                </div>
							<?php } ?>
                    </div>
                </div>
				<?php
			}
		}
	}, 10, 2 );