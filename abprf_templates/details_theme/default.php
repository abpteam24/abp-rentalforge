<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	$post_id = $post_id ?? get_the_id();
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	if ( $post_id > 0 ) {
		$abprf_infos   = ABPRF_Function::get_all_meta( $post_id );
		$sale_continue = array_key_exists( 'sale_continue', $abprf_infos ) ? $abprf_infos['sale_continue'] : 'on';
		?>
        <div id="abprf_area" class="abprf_area default_details_page">
            <div class="abprf_container">
                <div class="_abprf_row">
                    <div class="_fd_column_mar_b">
						<?php do_action( 'abprf_title', $abprf_infos ); ?>
						<?php do_action( 'abprf_sub_title', $abprf_infos ); ?>
                    </div>
                </div>
                <div class="_abprf_row">
                    <div class="_col_12">
                        <h2 class="_abprf_mar_b"><?php esc_html_e( 'Available Tools', 'abprf-rental-forge' ); ?></h2>
                        <div class="equipment_item_area">
							<?php
								$price_type = array_key_exists( 'price_type', $abprf_infos ) ? $abprf_infos['price_type'] : 'hourly';
								$infos      = array_key_exists( 'equipment_infos', $abprf_infos ) ? $abprf_infos['equipment_infos'] : [];
								if ( sizeof( $infos ) > 0 ) {
									foreach ( $infos as $info ) {
										$icon_image = array_key_exists( 'icon', $info ) ? $info['icon'] : '';
										$icon       = $image = $emoji = '';
										if ( is_numeric( $icon_image ) ) {
											$image = $icon_image;
										} elseif ( preg_match( '/\s/', $icon_image ) ) {
											$icon = $icon_image;
										} else {
											$emoji = $icon_image;
										}
										$name = array_key_exists( 'name', $info ) ? $info['name'] : '';
										?>
                                        <div class="equipment_item">
                                            <div class="item_head">
                                                <div class="item_img">
													<?php if ( $image ) {
														ABPRF_Layout::bg_image( '', $image );
													} else { ?>
                                                        <span class="_fs_h1 <?php echo esc_attr( $icon ); ?>"><?php echo esc_html( $emoji ); ?></span>
													<?php } ?>
                                                </div>
                                                <h4 class="_abprf"><?php echo esc_html( $name ); ?></h4>
                                                <p class="_abprf">DeWalt DCD996B</p>
                                            </div>
                                            <div class="item_body">
                                                <div class="item_spec">
                                                    <span class="spec_badge">820W</span>
                                                    <span class="spec_badge">2000 RPM</span>
                                                    <span class="spec_badge">20V MAX</span>
                                                    <span class="spec_badge">Battery Included</span>
                                                </div>
                                                <div class="pricing_box">
                                                    <div class="price_row">
                                                        <span class="price_label">Hourly Rate</span>
                                                        <span class="price_value" data-rate="350">৳350/hr</span>
                                                    </div>
                                                    <div class="item_condition">Min. 2 hours • Deposit: ৳2,000</div>
                                                    <div class="calculated_cost">
                                                        <div class="cost_label">Total for 4 hours:</div>
                                                        <div class="cost_value">৳1,400</div>
                                                    </div>
                                                </div>
                                                <label class="select_checkbox">
                                                    <input type="checkbox" class="item_checkbox" data-tool="Hammer Drill" data-rate="350">
                                                    <span class="checkbox_label">Select this tool</span>
                                                </label>
                                            </div>
                                        </div>
									<?php }
								} ?>
                        </div>
                    </div>
                </div>
                <div class="_abprf_row details_page_top">
                    <div class="_col_4_12_800_bg_border_all_center"> <?php do_action( 'abprf_slider', $abprf_infos ); ?></div>
                    <div class="details_page_info _col_4_12_800 _all_center">
                        <div>
                            <div class="_fd_column_mar_t_xxs title_details">
								<?php do_action( 'abprf_category', $abprf_infos ); ?>
								<?php do_action( 'abprf_capacity', $abprf_infos ); ?>
                            </div>
                            <div class="_divider_xs"></div>
                            <div class="_f_wrap">
								<?php do_action( 'abptm_route_direction', $abprf_infos ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="_col_4_12_800_bg_border_all_center">
						<?php if ( $sale_continue == 'on' ) {
							//do_action('abprf_search_form', $abprf_infos, ['form' => 'column'], $form_data);
						} else {
							ABPRF_Layout::layout_warning_info( 'sale_close_msg' );
						}
						?>
                    </div>
                </div>
				<?php do_action( 'abptm_the_content', $abprf_infos ); ?>
                <div class="_abprf_row">
					<?php if ( $sale_continue == 'on' ) { ?>
                        <div class=" abprf_rental_result">
							<?php //ABPRF_Layout::transport_list($form_data); ?>
                        </div>
					<?php } else {
						ABPRF_Layout::layout_warning_info( 'sale_close_msg' );
					}
					?>
                </div>
            </div>
        </div>
		<?php
	}
