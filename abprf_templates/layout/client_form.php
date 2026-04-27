<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_client_form_template', function ( $post_id, $abprf_infos = [] ) {
		if ( ! empty( $post_id ) && $post_id > 0 ) {
			$display                  = array_key_exists( 'display_client_form', $abprf_infos ) ? $abprf_infos['display_client_form'] : ABPRF_Function::get_post_info( $post_id, 'display_client_form', 'on' );
			$active_global = array_key_exists( 'active_global_form', $abprf_infos ) ? $abprf_infos['active_global_form'] : ABPRF_Function::get_post_info( $post_id, 'active_global_form', 'on' );
			if ( $active_global == 'on' ) {
				$forms = ABPRF_Function::get_option( 'abprf_forms' );
			} else {
				$forms = array_key_exists( 'abprf_forms', $abprf_infos ) ? $abprf_infos['abprf_forms'] : ABPRF_Function::get_post_info( $post_id, 'abprf_forms', [] );
			}
			if ( $display == 'on' && sizeof( $forms ) > 0 ) {
				?>
                <div class="client_info_area">
                    <div class="_box_1 attendee_item" >
                        <h5 class=" _abprf_title"> <?php esc_html_e('Client Info : ', 'abprf-rental-forge'); ?>&nbsp;<span class="_color_theme attendee_seat_name"></span></h5>
	                    <?php foreach ($forms as $id => $form) {
		                    ABPRF_Layout::create_client_form($form, $id );
	                    } ?>
                    </div>
                </div>
				<?php
			}
		}
	}, 10, 2 );