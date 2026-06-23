<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_client_form_template', function ( $post_id, $abprf_infos = [] ) {
		if ( ABPRF_Function::on_off( 'client_info' ) ) {
			$post_id = absint( $post_id );
			if ( $post_id <= 0 ) {
				return;
			}
			$display       = $abprf_infos['display_client_form'] ?? ABPRF_Function::get_post_info( $post_id, 'display_client_form', 'on' );
			$active_global = $abprf_infos['active_global_form'] ?? ABPRF_Function::get_post_info( $post_id, 'active_global_form', 'on' );
			if ( $active_global === 'on' ) {
				$forms = ABPRF_Function::get_option( 'abprf_forms' );
			} else {
				$forms = $abprf_infos['abprf_forms'] ?? ABPRF_Function::get_post_info( $post_id, 'abprf_forms', [] );
			}
			if ( $display === 'on' && ! empty( $forms ) && is_array( $forms ) ) {
				?>
                <div class="client_info_area">
                    <div class="item_box_1 attendee_item">
                        <h5 class=" _abprf_title">
							<?php esc_html_e( 'Client Info : ', 'abp-rentalforge' ); ?>&nbsp;<span class="_color_theme attendee_seat_name"></span>
                        </h5>
						<?php
							foreach ( $forms as $id => $form ) {
								ABPRF_Layout::create_client_form( $form, $id );
							}
						?>
                    </div>
                </div>
				<?php
			}
		}
	}, 10, 2 );