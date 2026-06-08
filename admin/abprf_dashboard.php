<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Dashboard' ) ) {
		class ABPRF_Dashboard {
			public function __construct() {
				add_action( 'abprf_load_dashboard', [ $this, 'load_dashboard' ] );
			}

			public function load_dashboard( $abprf_info ): void {
				$total_post     = isset( $abprf_info['total_post'] ) && $abprf_info['total_post'] ? $abprf_info['total_post'] : 0;
				$total_property = isset( $abprf_info['total_property'] ) && $abprf_info['total_property'] ? $abprf_info['total_property'] : 0;
				?>
                <div class="abprf_dashboard">
                    <div class="dashboard_head _f_wrap">
                        <div class="_section_card" data-href="<?php echo esc_url( add_query_arg( 'rf_tab', 'posts' ) ); ?>">
                            <div class="_d_flex">
                                <h2 class="_abprf_all_center_mar_r_xs">🏘️</h2>
                                <div class="_fd_column">
                                    <h5 class="_abprf"><?php esc_html_e( 'Post', 'abp-rentalforge' ); ?></h5>
                                    <h4 class="_abprf_color_theme"><?php echo esc_html( $total_post ); ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="_section_card" data-href="<?php echo esc_url( add_query_arg( 'rf_tab', 'properties' ) ); ?>">
                            <div class="_d_flex">
                                <h2 class="_abprf_all_center_mar_r_xs">🏠</h2>
                                <div class="_fd_column">
                                    <h5 class="_abprf"><?php esc_html_e( 'Properties', 'abp-rentalforge' ); ?></h5>
                                    <h4 class="_abprf_color_theme"><?php echo esc_html( $total_property ); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}
		}
		new ABPRF_Dashboard ();
	}