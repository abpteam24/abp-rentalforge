<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_rental_duration_template', function ( $abprf_infos = [] ) {
		$date_infos = array_key_exists( 'date_info', $abprf_infos ) ? $abprf_infos['date_info'] : '';
		if ( is_array( $date_infos ) && sizeof( $date_infos ) > 0 ) {
			$dif_text   = array_key_exists( 'text', $date_infos ) ? $date_infos['text'] : '';
			$start_time = array_key_exists( 'start_time', $abprf_infos ) ? $abprf_infos['start_time'] : '';
			$end_time   = array_key_exists( 'end_time', $abprf_infos ) ? $abprf_infos['end_time'] : '';
			?>
            <div class="duration_area">
                <h5 class="_abprf"><?php esc_html_e( 'Total Rental Duration', 'abp-rentalforge' ); ?></h5>
                <h2 class="_abprf"><?php echo esc_html( $dif_text ); ?></h2>
                <h6 class="_abprf">
					<?php echo esc_html( ABPRF_Function::date_format( $start_time ) ) . '  →  ' . esc_html( ABPRF_Function::date_format( $end_time ) ); ?>
                </h6>
            </div>
			<?php
		}
	}, 10, 2 );