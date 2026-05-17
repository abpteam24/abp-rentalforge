<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_property_item_template', function ( $abprf_infos, $property = [] ) {
		if ( is_array( $property ) && sizeof( $property ) > 0 ) {
			$location       = array_key_exists( 'location', $abprf_infos ) ? $abprf_infos['location'] : [];
			$date_time_info = array_key_exists( 'date_info', $abprf_infos ) ? $abprf_infos['date_info'] : [];
			$abprf_brand    = array_key_exists( 'abprf_brand', $abprf_infos ) ? $abprf_infos['abprf_brand'] : ABPRF_Function::get_option( 'abprf_brand' );
			$dif            = array_key_exists( 'dif', $date_time_info ) ? $date_time_info['dif'] : 0;
			$dif_text       = array_key_exists( 'dif_text', $date_time_info ) ? $date_time_info['dif_text'] : '';
			$post_id        = array_key_exists( 'post_id', $property ) ? $property['post_id'] : '';
			$rent_rule      = array_key_exists( 'rent_rule', $property ) ? $property['rent_rule'] : '';
			$price_qty_info = array_key_exists( 'price_qty_info', $property ) ? $property['price_qty_info'] : '';
			$price_qty_info = ! empty( $price_qty_info ) ? json_decode( $price_qty_info, true ) : [];
			$price_qty_info = ! empty( $price_qty_info ) && ! empty( $location ) && array_key_exists( $location, $price_qty_info ) ? $price_qty_info[ $location ] : $price_qty_info;
			$price_info     = ! empty( $rent_rule ) && array_key_exists( $rent_rule, $price_qty_info ) ? $price_qty_info[ $rent_rule ] : [];
			$others         = array_key_exists( 'others', $property ) ? $property['others'] : '';
			$others         = ! empty( $others ) ? json_decode( $others, true ) : [];
			if ( ! empty( $rent_rule ) && ! empty( $price_info ) ) {
				$brand = array_key_exists( 'brand', $property ) ? $property['brand'] : '';
				$min   = array_key_exists( 'min', $price_info ) ? $price_info['min'] : '';
				$max   = array_key_exists( 'max', $price_info ) ? $price_info['max'] : '';
				if ( ! empty( $max ) ) {
					$dif_exit = $min <= $dif && $max >= $dif ? 1 : 0;
				} else {
					$dif_exit = $min <= $dif ? 1 : 0;
				}
				$price       = array_key_exists( 'price', $price_info ) ? $price_info['price'] : '';
				$price       = apply_filters( 'abprf_filter_price', $price, $rent_rule, $price_info );
				$price       = ABPRF_Function::tax_with_price( $post_id, $price );
				$total_price = $price * $dif;
				?>
                <div class="property_item _box_1">
                    <div class="item_head">
                        <div class="item_img _all_center">
							<?php ABPRF_Layout::image_icon( array_key_exists( 'icon', $others ) ? $others['icon'] : '' ); ?>
                        </div>
                        <h4 class="_abprf"><?php echo esc_html( array_key_exists( 'name', $property ) ? $property['name'] : '' ); ?></h4>
						<?php if ( ! empty( $brand ) ) { ?>
                            <p class="_abprf"><?php echo esc_html( array_key_exists( $brand, $abprf_brand ) && array_key_exists( 'name', $abprf_brand[ $brand ] ) ? $abprf_brand[ $brand ]['name'] : '' ); ?></p>
						<?php } ?>
                    </div>
                    <div class="item_body">
						<?php ABPRF_Layout::item_feature( $abprf_infos, $property ); ?>
                        <div class="pricing_box">
                            <div class="price_row">
                                <span class="price_label"><?php echo esc_html( ABPRF_Layout::rent_rules( $rent_rule ) ); ?></span>
                                <span class="price_value">
                                    <?php echo wp_kses_post( wc_price( $price ) ); ?>
                                    <?php echo esc_html( ABPRF_Layout::per_rent_rules( $rent_rule ) ); ?>
                                </span>
                            </div>
                            <div class="item_condition">
								<?php $property_condition = ABPRF_Layout::item_condition( $rent_rule, $min, $max );
									echo esc_html( $property_condition ); ?>
                            </div>
							<?php ABPRF_Layout::item_deposit( $price_qty_info ); ?>
							<?php ABPRF_Layout::item_cost( $date_time_info, $dif_exit, $dif_text, $total_price, $property_condition ); ?>
                        </div>
						<?php
							if ( is_array( $date_time_info ) && sizeof( $date_time_info ) > 0 && $dif_exit > 0 ) {
								ABPRF_Layout::item_select_property( $property, $price_info, $total_price );
							}
						?>
                    </div>
                </div>
				<?php
			}
		}
	}, 10, 2 );