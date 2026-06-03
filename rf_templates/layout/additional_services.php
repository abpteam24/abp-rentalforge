<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_additional_template', function ( $post_id, $abprf_infos = [] ) {
		if ( ! empty( $post_id ) && $post_id > 0 ) {
			$display                  = array_key_exists( 'display_additional_services', $abprf_infos ) ? $abprf_infos['display_additional_services'] : ABPRF_Function::get_post_info( $post_id, 'display_additional_services', 'on' );
			$active_global_additional = array_key_exists( 'active_global_additional', $abprf_infos ) ? $abprf_infos['active_global_additional'] : ABPRF_Function::get_post_info( $post_id, 'active_global_additional', 'on' );
			if ( $active_global_additional == 'on' ) {
				$additional_services = ABPRF_Function::get_option( 'abprf_additional' );
			} else {
				$additional_services = array_key_exists( 'abprf_additional', $abprf_infos ) ? $abprf_infos['abprf_additional'] : ABPRF_Function::get_post_info( $post_id, 'abprf_additional', [] );
			}
			if ( $display == 'on' && sizeof( $additional_services ) > 0 ) {
				$ex_count = 0;
				?>
                <div class="additional_service_area">
                    <div class="item_box_1 additional_service">
                        <h5 class="_abprf_title"> <?php esc_html_e( 'Additional services ( Optional ) : ', 'abprf-rental-forge' ); ?></h5>
						<?php foreach ( $additional_services as $id => $service ) {
							$icon_image = array_key_exists( 'icon', $service ) ? $service['icon'] : '';
							$name       = array_key_exists( 'name', $service ) ? $service['name'] : '';
							$price      = array_key_exists( 'price', $service ) ? $service['price'] : '';
							$tax_price  = ! empty( $price ) && $price > 0 ? ABPRF_Function::tax_with_price( $post_id, $price ) : 0;
							$qty        = array_key_exists( 'qty', $service ) ? $service['qty'] : '';
							$max_qty    = array_key_exists( 'max_qty', $service ) ? $service['max_qty'] : '';
							$returnable = array_key_exists( 'returnable', $service ) ? $service['returnable'] : 'no';
							if ( empty( $qty ) ) {
								$qty      = !empty($max_qty)?$max_qty:9999;
							}
							$sold_qty = 0;
                            if($qty<9999){
                                $abprf_infos['ex_id']=$id;
	                            $sold_qty      = ABPRF_Query::get_sold_qty_ex( $abprf_infos );
                            }
							$available   = $qty - $sold_qty;
							$max_qty     = ! empty( $max_qty ) ? min( $max_qty, $available ) : $available;
							$description = array_key_exists( 'description', $service ) ? $service['description'] : '';
							if ( $ex_count > 0 ) { ?>
                                <div class="_divider_xs"></div>
							<?php }
							$ex_count ++; ?>
                            <div class="service_item _d_flex">
                                <div class="_w_100 _fs_h3_all_center"><?php ABPRF_Layout::image_icon( $icon_image ); ?></div>
                                <div class="_fd_column_w_full">
                                    <div class="_fj_between">
                                        <h6 class="_abprf_fa_center"> <?php echo esc_html( $name ); ?></h6>
										<?php if ( $available > 0 ) { ?>
                                            <input type="hidden" name="<?php echo esc_attr( 'name_' . $id ); ?>" value="<?php echo esc_attr( $name ); ?>"/>
											<?php
											$input_info = [ 'name' => 'qty_' . $id, 'price' => $tax_price, 'min_qty' => 0, 'max_qty' => $max_qty, 'class' => 'ex_price_calculate', ];
											ABPRF_Layout::quantity_input( $input_info );
										} else { ?>
                                            <span class="_color_warning"> <?php esc_html_e( 'Not Available !', 'abprf-rental-forge' ); ?></span>
										<?php } ?>
                                    </div>
                                    <h5 class="_abprf_color_theme">
										<?php
											if ( $tax_price > 0 ) {
												echo wp_kses_post( wc_price( $tax_price ) );
											} else {
												esc_html_e( 'Free', 'abprf-rental-forge' );
											}
											if ( $returnable == 'yes' ) {
												?> <span class="trash"><?php esc_html_e( 'Returnable', 'abprf-rental-forge' ); ?></span><?php
											} else {
												?><span class="publish"><?php esc_html_e( 'Nor-Returnable', 'abprf-rental-forge' ); ?></span>  <?php
											}
										?>
                                    </h5>
                                    <p class="_abprf"><?php echo esc_html( $description ); ?></p>
                                </div>
                            </div>
						<?php } ?>
                    </div>
                </div>
				<?php
			}
		}
	}, 10, 2 );