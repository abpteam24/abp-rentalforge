<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Category' ) ) {
		class ABPRF_Category {
			public function __construct() {
				add_action( 'abprf_load_category', array( $this, 'load_category' ) );
				add_action( 'wp_ajax_abprf_save_category', array( $this, 'save_category' ) );
			}

			public function load_category( $abprf_info ) {
				$category_label = isset( $abprf_info['category_label'] ) && $abprf_info['category_label'] ? $abprf_info['category_label'] : __( 'Category', 'abprf-rental-forge' );
				?>
                <div class="_section_xs ">
                    <div class="category_list">
						<?php echo '<pre>';
							print_r( ABPRF_Function::get_taxonomy( 'abprf_category' ) );
							echo '</pre>'; ?>
                    </div>
                    <button type="button" class="_btn_default" data-target-popup="#abprf_category_popup"><span class="_mar_r_xs">➕</span><?php echo esc_html__( 'Add New', 'abprf-rental-forge' ) . ' ' . esc_html( $category_label ); ?></button>
                </div>
                <div class="abprf_popup abprf_area abprf_category_popup" data-popup="#abprf_category_popup">
                    <div class="popup_area">
                        <span class="popup_close"><i class="fas fa-times"></i></span>
                        <div class="popup_body">
                            <form class="save_category" method="post" action="">
                                <div class="_setting_item">
                                    <label class="_f_equal_f_wrap">
                                        <span class="_mar_r_xs"><?php echo esc_html( $category_label ) . ' ' . esc_html__( 'Name', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                                        <input class="_form_control" name="name" placeholder="<?php esc_attr_e( 'Name', 'abprf-rental-forge' ); ?>" required/>
                                    </label>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'sub_title' ); ?>
                                </div>
                                <div class="_setting_item">
                                    <label class="_f_equal_f_wrap">
                                        <span class="_mar_r_xs"><?php echo esc_html( $category_label ) . ' ' . esc_html__( 'Slug (Optional)', 'abprf-rental-forge' ); ?></span>
                                        <input class="_form_control" name="slug" placeholder="<?php esc_attr_e( 'Slug', 'abprf-rental-forge' ); ?>"/>
                                    </label>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'sub_title' ); ?>
                                </div>
                                <div class="_setting_item">
                                    <label class="_f_equal_f_wrap">
                                        <span class="_mar_r_xs"><?php echo esc_html( $category_label ) . ' ' . esc_html__( 'Description', 'abprf-rental-forge' ); ?></span>
                                        <textarea class="_form_control" name="description" placeholder="<?php esc_attr_e( 'Description', 'abprf-rental-forge' ); ?>"></textarea>
                                    </label>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'sub_title' ); ?>
                                </div>
                                <div class="_divider_xs"></div>
                                <button type="submit" class="_btn_theme"><span class="_mar_r_xxs">💾</span><?php echo esc_html__( 'Save', 'abprf-rental-forge' ) . ' ' . esc_html( $category_label ); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function save_category() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
					$slug        = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
					$description = isset( $_POST['description'] ) ? sanitize_text_field( wp_unslash( $_POST['description'] ) ) : '';
					if ( ! empty( $name ) ) {
						$result = wp_insert_term(
							$name,
							'abprf_category',
							array(
								'slug' => $slug,
								'description' => $description,
							)
						);
						if ( is_wp_error( $result ) ) {
							wp_send_json_success( $result->get_error_message() );
						} else {
							wp_send_json_success( esc_html__( 'Saved Successfully ! ', 'abprf-rental-forge' ) );
						}
					}
				} else {
					wp_send_json_success( esc_html__( 'not Saved !', 'abprf-rental-forge' ) );
				}
				wp_die();
			}
		}
		new ABPRF_Category();
	}