<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_template_property_item', function ( $post_id, $property = [] ) {
			if ( ! empty( $post_id ) && $post_id > 0 && ! empty( $property ) && is_array( $property ) && sizeof( $property ) > 0 ) {
				$icon_image = array_key_exists( 'icon', $property ) ? $property['icon'] : '';
				$name       = array_key_exists( 'name', $property ) ? $property['name'] : '';
				$brand      = array_key_exists( 'brand', $property ) ? $property['brand'] : '';
				$features   = array_key_exists( 'features', $property ) ? $property['features'] : '';
				$features   = ! empty( $features ) ? json_decode( $features, true ) : [];
				$qty_info   = array_key_exists( 'qty_info', $property ) ? $property['qty_info'] : '';
				$qty_info   = ! empty( $qty_info ) ? json_decode( $qty_info, true ) : [];
				$price_rule = array_key_exists( 'price_rule', $property ) ? $property['price_rule'] : '';
				$price_rule = $price_rule ? explode( ',', $price_rule ) : [];
				$price_info = array_key_exists( 'price_info', $property ) ? $property['price_info'] : '';
				$price_info = ! empty( $price_info ) ? json_decode( $price_info, true ) : [];
				?>
                <div class="property_item">
                    <div class="item_head">
                        <div class="item_img _all_center">
							<?php ABPRF_Layout::image_icon( $icon_image ); ?>
                        </div>
                        <h4 class="_abprf"><?php echo esc_html( $name ); ?></h4>
						<?php if ( $brand ) { ?>
                            <p class="_abprf"><?php echo esc_html( $brand ); ?></p>
						<?php } ?>
                    </div>
                    <div class="item_body">
						<?php if ( ! empty( $features ) && is_array( $features ) && sizeof( $features ) > 0 ) { ?>
                            <div class="item_spec">
								<?php foreach ( $features as $feature ) {
									$value = is_array( $feature ) && array_key_exists( 'value', $feature ) ? $feature['value'] : '';
									if ( $value ) { ?>
                                        <span class="spec_badge"><?php echo esc_html( $value ); ?></span>
									<?php }
								} ?>
                            </div>
						<?php } ?>
                        <div class="pricing_box">
							<?php if ( sizeof( $price_rule ) > 0 ) {
								$deposit_info   = array_key_exists( 'deposit', $price_info ) ? $price_info['deposit'] : [];
								$deposit_type   = is_array( $deposit_info ) && array_key_exists( 'type', $deposit_info ) ? $deposit_info['type'] : '';
								$deposit_value  = is_array( $deposit_info ) && array_key_exists( 'value', $deposit_info ) ? $deposit_info['value'] : '';
								$active_deposit = $deposit_type && $deposit_value ? 'on' : 'off';
								$hourly_info    = in_array( 'hourly', $price_rule ) && array_key_exists( 'hourly', $price_info ) ? $price_info['hourly'] : [];
								$price_hourly   = is_array( $hourly_info ) && array_key_exists( 'price', $hourly_info ) ? $hourly_info['price'] : '';
								if ( ! empty( $price_hourly ) ) {
									$min_hour = is_array( $hourly_info ) && array_key_exists( 'min', $hourly_info ) ? $hourly_info['min'] : 1;
									$price    = apply_filters( 'abprf_filter_hourly_price', $price_hourly, $property );
									$price    = ABPRF_Function::tax_with_price( $post_id, $price ); ?>
                                    <div class="price_row">
                                        <div class="_fd_column">
                                            <span class="price_label"><?php esc_html_e( 'Hourly Rate', 'abprf-rental-forge' ); ?></span>
                                            <span class="item_condition"><?php echo esc_html( sprintf(
												/* translators: %s = minimum number of hours */
													_n( 'Min. %s hour', 'Min. %s hours', $min_hour, 'abprf-rental-forge' ), $min_hour ) ); ?></span>
                                        </div>
                                        <span class="price_value"><?php echo wp_kses_post( wc_price( $price ) ) . esc_html__( '/hr', 'abprf-rental-forge' ); ?></span>
                                    </div>
								<?php } ?>
								<?php
								$daily_info  = in_array( 'daily', $price_rule ) && array_key_exists( 'daily', $price_info ) ? $price_info['daily'] : [];
								$price_daily = is_array( $daily_info ) && array_key_exists( 'price', $daily_info ) ? $daily_info['price'] : '';
								if ( ! empty( $price_daily ) ) {
									$min_day = is_array( $daily_info ) && array_key_exists( 'min', $daily_info ) ? $daily_info['min'] : 1;
									$price   = apply_filters( 'abprf_filter_daily_price', $price_daily, $property );
									$price   = ABPRF_Function::tax_with_price( $post_id, $price ); ?>
                                    <div class="price_row">
                                        <div class="_fd_column">
                                            <span class="price_label"><?php esc_html_e( 'Daily Rate', 'abprf-rental-forge' ); ?></span>
                                            <span class="item_condition"><?php echo esc_html( sprintf(
												/* translators: %s = minimum number of days */
													_n( 'Min. %s Day', 'Min. %s Days', $min_day, 'abprf-rental-forge' ), $min_day ) ); ?></span>
                                        </div>
                                        <span class="price_value"><?php echo wp_kses_post( wc_price( $price ) ) . esc_html__( '/Day', 'abprf-rental-forge' ); ?></span>
                                    </div>
								<?php } ?>
								<?php
								$monthly_info  = in_array( 'monthly', $price_rule ) && array_key_exists( 'monthly', $price_info ) ? $price_info['monthly'] : [];
								$price_monthly = is_array( $monthly_info ) && array_key_exists( 'price', $monthly_info ) ? $monthly_info['price'] : '';
								if ( ! empty( $price_monthly ) ) {
									$min_month = is_array( $monthly_info ) && array_key_exists( 'min', $monthly_info ) ? $monthly_info['min'] : 1;
									$price     = apply_filters( 'abprf_filter_monthly_price', $price_monthly, $property );
									$price     = ABPRF_Function::tax_with_price( $post_id, $price ); ?>
                                    <div class="price_row">
                                        <div class="_fd_column">
                                            <span class="price_label"><?php esc_html_e( 'Monthly Rate', 'abprf-rental-forge' ); ?></span>
                                            <span class="item_condition"><?php echo esc_html( sprintf(
												/* translators: %s = minimum number of month */
													_n( 'Min. %s Month', 'Min. %s Months', $min_month, 'abprf-rental-forge' ), $min_month ) ); ?></span>
                                        </div>
                                        <span class="price_value"><?php echo wp_kses_post( wc_price( $price ) ) . esc_html__( '/Month', 'abprf-rental-forge' ); ?></span>
                                    </div>
								<?php } ?>
								<?php if ( $active_deposit == 'on' ) { ?>
                                    <div class="item_condition">
										<?php if ( $deposit_type == 'fixed' ) {
											echo wp_kses_post( sprintf(
											/* translators: %s = deposit label' */
												_x( '• Deposit: %s Fixed', 'deposit label', 'abprf-rental-forge' ), wc_price( $deposit_value ) ) );
										} elseif ( $deposit_type == 'percent' ) {
											echo esc_html( sprintf(
											/* translators: %s = deposit label' */
												_x( '• Deposit: %s of Total Price', 'deposit label', 'abprf-rental-forge' ), esc_html( $deposit_value . '%' ) ) );
										} else {
											echo wp_kses_post( sprintf(
											/* translators: %s = deposit label' */
												_x( '• Deposit: %s Per Item', 'deposit label', 'abprf-rental-forge' ), wc_price( $deposit_value ) ) );;
										}
										?>
                                    </div>
								<?php } ?>
							<?php } ?>
                            <div class="calculated_cost">
                                <div class="cost_label">Total for 4 hours:</div>
                                <div class="cost_value">৳1,400</div>
                            </div>
                        </div>
                        <label class="select_checkbox">
                            <input type="checkbox" class="item_checkbox" data-tool="Hammer Drill" data-rate="350">
                            <span class="checkbox_label"><?php echo esc_html__( 'Select ', 'abprf-rental-forge' ) . esc_html( $name ); ?></span>
                        </label>
                    </div>
                </div>
				<?php
			}
	}, 10, 2 );