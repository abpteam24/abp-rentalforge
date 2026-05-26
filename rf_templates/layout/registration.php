<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_registration_template', function ( $abprf_infos = [] ) {
		$rent_rule                   = array_key_exists( 'rent_rule', $abprf_infos ) ? $abprf_infos['rent_rule'] : 'hourly';
		$post_id                     = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : '';
		$location                    = array_key_exists( 'location', $abprf_infos ) ? $abprf_infos['location'] : '';
		$template                    = array_key_exists( 'abprf_template', $abprf_infos ) ? $abprf_infos['abprf_template'] : 'grid';
		$filter_arg['rent_continue'] = 'on';
		$filter_arg['status']        = 'publish';
		if ( ! empty( $post_id ) ) {
			$filter_arg['post_id'] = $post_id;
			if ( ! empty( $rent_rule ) ) {
				$filter_arg['rent_rule'] = $rent_rule;
			}
			if ( ! empty( $location ) ) {
				$filter_arg['location'] = $location;
			}
			$properties = ABPRF_Query::get_property( $filter_arg );
			//echo '<pre>';print_r($properties);echo '</pre>';
			?>
            <div class="abprf_booking">
                <form class="" action="" method="post">
					<?php wp_nonce_field( 'abprf_registration_nonce' );
						do_action( 'abprf_admin_order', $post_id );
						if ( empty( $template ) || $template == 'grid' ) { ?>
                            <div class="post_top_filter">
                                <h3 class="_abprf"><?php esc_html_e( 'Available Property', 'abprf-rental-forge' ); ?></h3>
                                <div class="_group_content">
                                    <button type="button" class="_btn_info_xs_fs_h6 grid_view rf_active"><span class="fas fa-table-cells"></span></button>
                                    <button type="button" class="_btn_info_xs_fs_h6 list_view"><span class="fas fa-list"></span></button>
                                </div>
                            </div>
							<?php if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) { ?>
                                <div class="property_item_area abprf_grid item_3">
									<?php foreach ( $properties as $property ) {
										do_action( 'abprf_property_item', $abprf_infos, $property );
									} ?>
                                </div>
							<?php } else {
								ABPRF_Layout::layout_warning_info( 'no_property_found' );
							} ?>
						<?php } else { ?>
                            <div class="property_item_group_area item_box_1">
                                <h5 class="_abprf_title"><?php esc_html_e( 'Available Property', 'abprf-rental-forge' ); ?></h5>
                                <div class="property_item_area">
									<?php if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
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
		}
	}, 10, 2 );
