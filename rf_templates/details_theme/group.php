<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_details_group_template', function ( $post_id ) {
		if ( $post_id > 0 ) {
			$abprf_infos         = ABPRF_Function::get_all_meta( $post_id );
			$rent_continue       = $abprf_infos['rent_continue'] ?? 'on';
			$abprf_infos['form'] = 'inline';
			//echo '<pre>';print_r($abprf_infos);echo '</pre>';
			?>
            <div id="abprf_area" class="abprf_area default_details_page">
                <div class="abprf_container">
                    <div class="_abp_row">
                        <div class="_fd_column_mar_b">
							<?php do_action( 'abprf_title', $post_id, $abprf_infos );
								do_action( 'abprf_sub_title', $post_id, $abprf_infos ); ?>
                        </div>
                    </div>
                    <div class="_abp_row">
                        <div class="_col_12">
							<?php if ( $rent_continue == 'on' ) {
								do_action( 'abprf_search_form', $abprf_infos );
							} else {
								ABPRF_Layout::layout_warning_info( 'sale_close_msg' );
							} ?>
                        </div>
                    </div>
                    <div class="_abp_row">
                        <div class="_col_12">
							<?php do_action( 'abprf_registration', $abprf_infos ); ?>
                        </div>
                    </div>
                    <div class="_abp_row">
                        <div class="_col_12">
							<?php do_action( 'abprf_content', $post_id ); ?>
                        </div>
                    </div>
                    <div class="_abp_row">
                        <div class="_col_12"> <?php do_action( 'abprf_slider', ( $abprf_infos['abprf_sliders'] ?? [] ) ); ?></div>
                    </div>
                    <div class="_abp_row">
                        <div class="_col_12"> <?php do_action( 'abprf_faq', $abprf_infos ); ?></div>
                    </div>
                    <div class="_abp_row">
                        <div class="_col_12"> <?php do_action( 'abprf_term_condition', $abprf_infos ); ?></div>
                    </div>
                    <div class="_abp_row">
                        <div class="_col_12"> <?php do_action( 'abprf_related_item', ( $abprf_infos['related_item'] ?? '' ) ); ?></div>
                    </div>
                </div>
            </div>
			<?php
		}
	} );
