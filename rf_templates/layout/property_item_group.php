<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_property_item_group_template', function ( $abprf_infos, $properties = [] ) {
		//echo '<pre>';print_r($property);echo '</pre>';
		if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
			$ex_count = 0;
			foreach ( $properties as $property ) {
				if ( is_array( $property ) && sizeof( $property ) > 0 ) {
					$location       = $abprf_infos['location'] ?? '';
					$start_time     = $abprf_infos['start_time'] ?? '';
					$end_time       = $abprf_infos['end_time'] ?? '';
					$post_id        = $property['post_id'] ?? '';
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
						//echo '<pre>';print_r($abprf_infos);echo '</pre>';
						if ( $ex_count > 0 ) { ?>
                            <div class="_divider_xs"></div>
						<?php }
						$ex_count ++;
						?>
                        <div class="property_item <?php echo esc_attr( $abprf_infos['add_class'] ?? '' ); ?>">
                            <div class="item_head"><?php ABPRF_Layout::image_icon( ($others['icon'] ?? '' ),''); ?></div>
                            <div class="property_details">
                                <div class="property_title_price">
                                    <div class="_fd_column">
                                        <h4 class="_abp">
											<?php echo esc_html( $property_name ); ?>
											<?php if ( ( $property['brand'] ?? '' ) && ABPRF_Function::on_off( 'brand' ) ) { ?>
                                                <small class="_abp _color_theme"><?php echo esc_html( ABPRF_Function::brand_value( $property['brand'] ?? '' ) ); ?></small>
											<?php } ?>
                                        </h4>
                                        <div class="pricing_box">
                                            <div class="item_condition">
												<?php echo esc_html( ABPRF_Layout::item_condition( $rent_rule, $price_info ) ); ?>
                                            </div>
											<?php ABPRF_Layout::item_deposit( $price_info ); ?>
											<?php if ( ABPRF_Function::on_off( 'property_des' ) && ($others['description'] ?? '') ) { ?>
                                                <div class="item_condition"><?php ABPRF_Layout::load_more( $others['description'] ?? ''  );?></div>
											<?php } ?>
                                        </div>
                                    </div>
                                    <div class="price_row">
										<?php ABPRF_Layout::item_price( $post_id, $rent_rule, $price_info ); ?>
                                    </div>
                                </div>
                                <div class="property_item_bottom">
									<?php ABPRF_Layout::item_feature( $property['features'] ?? '' );
										if ( ! empty( $start_time ) && ! empty( $end_time ) ) {
											ABPRF_Layout::item_cost( $abprf_infos, $price_info, $total_price, $time_duration );
											if ( ! empty( $time_duration ) ) {
												ABPRF_Layout::item_select_property( $abprf_infos, $price_info, $total_price );
											}
										}
									?>
                                </div>
                            </div>
                        </div>
						<?php
					}
				}
			}
		} else {
			ABPRF_Layout::layout_warning_info( 'no_property_found' );
		}
	}, 10, 2 );