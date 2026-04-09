<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'ABPRF_Orders' ) ) {
		class ABPRF_Orders {
			public function __construct() {
				add_action( 'abprf_load_orders', array( $this, 'load_orders' ) );
			}

			public function load_orders($abprf_info) {
					?>
                    <div class="_section_xs abprf_orders">
                        <h4 class="_abprf_color_theme"><span class="_mar_r_xxs">📋</span> <?php esc_html_e( 'Orders', 'abprf-rental-forge' ); ?></h4>
                        <div class="_divider_xs"></div>
                    </div>
					<?php
			}
		}
		new ABPRF_Orders();
	}