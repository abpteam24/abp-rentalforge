<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! function_exists( 'abprf_template_default' ) ) {
		function abprf_template_default( $post_id ): void {
			$post_id = $post_id ?? get_the_id();
			if ( $post_id > 0 ) {
				$abprf_infos         = ABPRF_Function::get_all_meta( $post_id );
				$rent_continue       = array_key_exists( 'rent_continue', $abprf_infos ) ? $abprf_infos['rent_continue'] : 'on';
				$abprf_infos['form'] = 'inline';
				//echo '<pre>';print_r(ABPRF_Function::time_slot_generate($times));echo '</pre>';
				?>
                <div id="abprf_area" class="abprf_area default_details_page">
                    <div class="abprf_container">
                        <div class="_abprf_row">
                            <div class="_fd_column_mar_b">
								<?php do_action( 'abprf_title', $post_id, $abprf_infos );
									do_action( 'abprf_sub_title', $post_id, $abprf_infos ); ?>
                            </div>
                        </div>
                        <div class="_abprf_row">
                            <div class="_col_12">
								<?php if ( $rent_continue == 'on' ) {
									do_action( 'abprf_search_form', $abprf_infos );
								} else {
									ABPRF_Layout::layout_warning_info( 'sale_close_msg' );
								}
								?>
                            </div>
                        </div>
                        <div class="_abprf_row">
                            <div class="_col_12">
								<?php do_action( 'abprf_registration', $abprf_infos ); ?>
                            </div>
                        </div>
                        <div class="_abprf_row">
                            <div class="_col_12">
								<?php do_action( 'abprf_content', $post_id ); ?>
                            </div>
                        </div>
                        <div class="_abprf_row">
                            <div class="_col_12_bg_border_all_center"> <?php do_action( 'abprf_slider', $abprf_infos ); ?></div>
                        </div>
                    </div>
                </div>
				<?php
			}
		}
	}
