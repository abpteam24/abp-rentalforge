<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! function_exists( 'abprf_template_default' ) ) {
		function abprf_template_default( $post_id ) {
			$post_id = $post_id ?? get_the_id();
			if ( $post_id > 0 ) {
				$abprf_infos   = ABPRF_Function::get_all_meta( $post_id );
				$rent_continue = array_key_exists( 'rent_continue', $abprf_infos ) ? $abprf_infos['rent_continue'] : 'on';
				$properties    = ABPRF_Query::get_property( [ 'post_id' => $post_id, 'rent_continue' => 'on', 'status' => 'publish' ] );
                $all_dates=ABPRF_Function::get_post_dates($post_id);
				//echo '<pre>';print_r($all_dates);echo '</pre>';
				?>
                <div id="abprf_area" class="abprf_area default_details_page">
                    <div class="abprf_container">
                        <div class="_abprf_row">
                            <div class="_fd_column_mar_b">
								<?php do_action( 'abprf_template_title', $post_id, $abprf_infos );
									do_action( 'abprf_template_sub_title', $post_id, $abprf_infos ); ?>
                            </div>
                        </div>
                        <div class="_abprf_row">
                            <div class="_col_12">
	                            <?php if ( $rent_continue == 'on' ) {
		                            do_action('abprf_template_search_form', $all_dates, ['form' => 'inline','post_id' => $post_id] );
	                            } else {
		                            ABPRF_Layout::layout_warning_info( 'sale_close_msg' );
	                            }
	                            ?>
                            </div>
                        </div>
                        <div class="_abprf_row">
                            <div class="_col_12">
                                <h2 class="_abprf_mar_b"><?php esc_html_e( 'Available Property', 'abprf-rental-forge' ); ?></h2>
                                <div class="property_item_area">
									<?php
										if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
											foreach ( $properties as $property ) {
												do_action( 'abprf_template_property_item', $post_id, $property );
											}
										} else {
											ABPRF_Layout::layout_warning_info( 'no_property_found' );
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

                            </div>
                        </div>
						<?php do_action( 'abptm_the_content', $abprf_infos ); ?>
                        <div class="_abprf_row">
							<?php if ( $rent_continue == 'on' ) { ?>
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
		}
	}
