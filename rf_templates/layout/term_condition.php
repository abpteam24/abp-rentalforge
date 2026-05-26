<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_term_condition_template', function ( $abprf_infos = [], $type = '' ) {
		$infos = [];
		if ( ! empty( $abprf_infos ) ) {
			$display           = array_key_exists( 'display_tc', $abprf_infos ) ? $abprf_infos['display_tc'] : 'on';
			$_tc              = array_key_exists( 'abprf_tc', $abprf_infos ) ? $abprf_infos['abprf_tc'] : '';
			$active_global_faq = array_key_exists( 'active_global_tc', $abprf_infos ) ? $abprf_infos['active_global_tc'] : 'on';
			$tc            = $active_global_faq == 'on' ? ABPRF_Function::get_option( 'abprf_tc' ,'') : $_tc;
			$infos         = $display == 'on' ? $tc : $infos;
		} else {
			$infos = $type == 'global' ? ABPRF_Function::get_option( 'abprf_tc','' ) : $infos;
		}
		if ( ! empty( $infos ) ) {
			?>
			<div class="_abp_panel">
				<div class="_panel_head">
					<h4 class="_abprf"><span class="_mar_r_xxs">🤝</span> <?php esc_html_e( 'Term & Conditions', 'abprf-rental-forge' ); ?></h4>
				</div>
				<div class="_panel_body_xs">
					<?php
						// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
						echo wp_kses_post( apply_filters( 'the_content', $infos ) ); ?>
				</div>
			</div>
			<?php
		}
	}, 10, 3 );
