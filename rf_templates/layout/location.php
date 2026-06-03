<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_location_template', function ( $post_id, $ribbon = '' ) {
		$all_locations    = ABPRF_Locations;
		$display_location = ABPRF_Function::get_post_info( $post_id, 'display_location', 'on' );
		$location_array   = ABPRF_Function::get_post_info( $post_id, 'abprf_location' );
		$location         = '';
		if ( ! empty( $location_array ) && $display_location == 'on' ) {
			$location_array = explode( ',', $location_array );
			if ( sizeof( $location_array ) > 1 ) {
				foreach ( $location_array as $loc_id ) {
					if ( array_key_exists( $loc_id, $all_locations ) && array_key_exists( 'name', $all_locations[ $loc_id ] ) ) {
						$location = ! empty( $location ) ? $location . ' - ' . $all_locations[ $loc_id ]['name'] : $all_locations[ $loc_id ]['name'];
					}
				}
				$location_label=__( 'Available Location : ', 'abprf-rental-forge' );
			} else {
				foreach ( $location_array as $loc_id ) {
					if ( array_key_exists( $loc_id, $all_locations ) && array_key_exists( 'name', $all_locations[ $loc_id ] ) ) {
						$location = $all_locations[ $loc_id ]['description'];
						if ( empty( $location ) ) {
							$location = $all_locations[ $loc_id ]['name'];
						}
					}
				}
				$location_label=__( 'Location : ', 'abprf-rental-forge' );
			}
			if ( $ribbon == 'ribbon' ) { ?>
                <div class="ribbon publish"><span class="_mar_r_xxs">📍</span><?php echo esc_html( $location ); ?></div>
			<?php } else { ?>
                <div class="item_location">
                    <i class="_mar_r_xxs">📍</i><span><?php echo esc_html( $location_label.' '.$location ); ?></span>
                </div>
				<?php
			}
		}
	}, 10, 3 );
