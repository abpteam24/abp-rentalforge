<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	add_action( 'abprf_location_template', function ( $post_id, $ribbon = '' ) {
		if ( ABPRF_Function::on_off( 'location' ) ) {
			$post_id = absint( $post_id );
			if ( $post_id <= 0 ) {
				return;
			}
			$all_locations    = ABPRF_Locations;
			$display_location = ABPRF_Function::get_post_info( $post_id, 'display_location', 'on' );
			$location_string  = ABPRF_Function::get_post_info( $post_id, 'abprf_location' );
			$location         = $location_label = '';
			if ( ! empty( $location_string ) && $display_location === 'on' ) {
				$location_array = explode( ',', $location_string );
				$location_count = count( $location_array );
				if ( $location_count > 1 ) {
					$loc_names = [];
					foreach ( $location_array as $loc_id ) {
						if ( isset( $all_locations[ $loc_id ]['name'] ) ) {
							$loc_names[] = $all_locations[ $loc_id ]['name'];
						}
					}
					$location       = implode( ' - ', $loc_names );
					$location_label = __( 'Available Location : ', 'abp-rentalforge' );
				} elseif ( $location_count === 1 ) {
					$loc_id = $location_array[0];
					if ( isset( $all_locations[ $loc_id ] ) ) {
						$location = ( $all_locations[ $loc_id ]['description'] ?? '' ) ?: ( $all_locations[ $loc_id ]['name'] ?? '' );
					}
					$location_label = __( 'Location : ', 'abp-rentalforge' );
				}
				if ( ! empty( $location ) ) {
					if ( $ribbon === 'ribbon' ) {
						?>
                        <div class="ribbon publish"><span class="_mar_r_xxs">📍</span><?php echo esc_html( $location ); ?></div>
						<?php
					} else {
						?>
                        <div class="item_location">
                            <i class="_mar_r_xxs">📍</i><span><?php echo esc_html( $location_label . ' ' . $location ); ?></span>
                        </div>
						<?php
					}
				}
			}
		}
	}, 10, 2 );