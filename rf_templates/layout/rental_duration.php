<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_rental_duration_template', function ( $date_infos = [] ) {
		if ( is_array( $date_infos ) && sizeof( $date_infos ) > 0 ) {
			$dif_text   = array_key_exists( 'text', $date_infos ) ? $date_infos['text'] : '';
			$start_time = array_key_exists( 'start_time', $date_infos ) ? $date_infos['start_time'] : '';
			$end_time   = array_key_exists( 'end_time', $date_infos ) ? $date_infos['end_time'] : '';
			//echo '<pre>';print_r( $date_infos );					echo '</pre>';
			?>
            <div class="duration_area">
                <h5 class="_abprf"><?php esc_html_e( 'Total Rental Duration', 'abprf-rental-forge' ); ?></h5>
                <h2 class="_abprf"><?php echo esc_html( $dif_text ); ?></h2>
                <h6 class="_abprf">
					<?php
						$date_format = ABPRF_Function::check_time_exit_date( $start_time ) ? 'full' : 'date';
						echo esc_html( ABPRF_Function::date_format( $start_time, $date_format ) ) . '  →  ' . esc_html( ABPRF_Function::date_format( $end_time, $date_format ) );
					?>
                </h6>
            </div>
			<?php
		}
	}, 10, 2 );