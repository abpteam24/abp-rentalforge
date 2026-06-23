<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_registration_template', function ( $abprf_infos = [] ) {
		$post_id = absint( $abprf_infos['post_id'] ?? 0 );
		if ( $post_id <= 0 ) {
			return;
		}
		$rent_rule = $abprf_infos['rent_rule'] ?? 'hourly';
		$location  = $abprf_infos['location'] ?? '';
		$template  = $abprf_infos['abprf_template'] ?? 'grid';
		$filter_arg = [
			'post_id' => $post_id,
			'rent_continue' => 'on',
			'status' => 'publish',
		];
		if ( ! empty( $rent_rule ) ) {
			$filter_arg['rent_rule'] = $rent_rule;
		}
		if ( ! empty( $location ) ) {
			$filter_arg['location'] = $location;
		}
		$properties = ABPRF_Query::get_property( $filter_arg );
		?>
        <div class="abprf_booking">
            <form action="" method="post">
				<?php
					wp_nonce_field( 'abprf_registration_nonce' );
					do_action( 'abprf_admin_order', $post_id );
					if ( $template === 'grid' || empty( $template ) ) {
						?>
                        <div class="post_top_filter">
                            <h3 class="_abprf"><?php esc_html_e( 'Available Property', 'abp-rentalforge' ); ?></h3>
                            <div class="_group_content">
                                <button type="button" class="_btn_light_info_xs_fs_h6 grid_view rf_active">
                                    <span class="fas fa-table-cells"></span>
                                </button>
                                <button type="button" class="_btn_light_info_xs_fs_h6 list_view">
                                    <span class="fas fa-list"></span>
                                </button>
                            </div>
                        </div>
						<?php if ( ! empty( $properties ) && is_array( $properties ) ) { ?>
                            <div class="property_item_area abprf_grid item_3">
								<?php
									foreach ( $properties as $property ) {
										do_action( 'abprf_property_item', $abprf_infos, $property );
									}
								?>
                            </div>
						<?php } else {
							ABPRF_Layout::layout_warning_info( 'no_property_found' );
						} ?>
					<?php } else { ?>
                        <div class="group_property item_box_1">
                            <h5 class="_abprf_title"><?php esc_html_e( 'Available Property', 'abp-rentalforge' ); ?></h5>
                            <div class="property_item_area">
								<?php if ( ! empty( $properties ) && is_array( $properties ) ) {
									do_action( 'abprf_property_item_group', $abprf_infos, $properties );
								} else {
									ABPRF_Layout::layout_warning_info( 'no_property_found' );
								} ?>
                            </div>
                        </div>
					<?php } ?>
                <div class="property_others"></div>
            </form>
        </div>
		<?php
	}, 10, 2 );