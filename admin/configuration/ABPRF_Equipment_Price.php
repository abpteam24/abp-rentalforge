<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_EquipmentPrice' ) ) {
		class ABPRF_EquipmentPrice {
			public function __construct() {
				add_action( 'abprf_post_content', [ $this, 'tab_content' ] );
			}

			public function tab_content( $abprf_infos ): void {
				$abprf_configuration = array_key_exists( 'abprf_configuration', $abprf_infos ) ? $abprf_infos['abprf_configuration'] : [];
				$equipment_icon      = isset( $abprf_configuration['equipment_icon'] ) && $abprf_configuration['equipment_icon'] ? $abprf_configuration['equipment_icon'] : 'fas fa-hammer';
				$post_title          = array_key_exists( 'post_title', $abprf_infos ) ? $abprf_infos['post_title'] : '';
				$price_type          = array_key_exists( 'price_type', $abprf_infos ) ? $abprf_infos['price_type'] : 'hourly';
				$infos               = array_key_exists( 'equipment_infos', $abprf_infos ) ? $abprf_infos['equipment_infos'] : [];
				?>
                <div class="tab_item abprf_equipment_price" data-tabs="#abprf_equipment_price">
                    <h4 class="_abprf_color_theme"><span class="<?php echo esc_attr( $equipment_icon ); ?> _mar_r_xs"></span> <?php echo esc_html( $post_title . ' ' . __( ' : ', 'abprf-rental-forge' ) . ' ' . __( 'Equipment and Price Configuration', 'abprf-rental-forge' ) ); ?></h4>
					<?php ABPRF_Layout::info_text( 'abprf_equipment_price' ); ?>
                    <div class="_divider_xs"></div>
                    <div class="_setting_item">
                        <label class="_f_equal_max_500_f_wrap">
                            <span class="_mar_r_xs"><?php esc_html_e( 'Price Type', 'abprf-rental-forge' ); ?></span>
                            <select class="_form_control " name="price_type" data-collapse-target required>
                                <option disabled selected><?php esc_html_e( 'Please Select', 'abprf-rental-forge' ); ?></option>
                                <option value="hourly" data-option-target-multi="#hourly_daily #hourly" <?php echo esc_attr( $price_type == 'hourly' ? 'selected' : '' ); ?>><?php esc_html_e( 'Hourly Rate', 'abprf-rental-forge' ); ?></option>
                                <option value="daily" data-option-target-multi="#hourly_daily #daily" <?php echo esc_attr( $price_type == 'daily' ? 'selected' : '' ); ?>><?php esc_html_e( 'Daily Rate', 'abprf-rental-forge' ); ?></option>
                                <option value="hourly_daily" data-option-target-multi="#hourly_daily #hourly #daily" <?php echo esc_attr( $price_type == 'hourly_daily' ? 'selected' : '' ); ?>><?php esc_html_e( 'Hourly & Daily Rate', 'abprf-rental-forge' ); ?></option>
                                <option value="monthly" data-option-target-multi="#monthly" <?php echo esc_attr( $price_type == 'monthly' ? 'selected' : '' ); ?>><?php esc_html_e( 'Monthly Rate', 'abprf-rental-forge' ); ?></option>
                            </select>
                        </label>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text( 'hourly_rate' ); ?>
						<?php ABPRF_Layout::info_text( 'daily_rate' ); ?>
						<?php ABPRF_Layout::info_text( 'hourly_daily' ); ?>
						<?php ABPRF_Layout::info_text( 'monthly_rate' ); ?>
                    </div>
                    <div class="_abprf_panel_xs abprf_configuration_content">
                        <div class="_panel_head">
                            <h5 class="_abprf"><?php esc_html_e( 'Equipment', 'abprf-rental-forge' ); ?></h5>
                            <div class="_divider_xs"></div>
	                        <?php ABPRF_Layout::info_text( 'equipment_icon' ); ?>
	                        <?php ABPRF_Layout::info_text( 'equipment_name' ); ?>
	                        <?php ABPRF_Layout::info_text( 'equipment_brand' ); ?>
                        </div>
                        <div class="_panel_body abprf_insert_item abprf_sortable equipment_area">
							<?php
								if ( sizeof( $infos ) > 0 ) {
									foreach ( $infos as $key => $info ) {
										$this->equipment_item( $abprf_infos, $info, $key );
									}
								}
							?>
                        </div>
                        <div class="_panel_footer">
							<?php ABPRF_Layout::button_add( __( 'Add New Equipment', 'abprf-rental-forge' ) ); ?>
                            <div class="abprf_d_none">
                                <div class="abprf_hidden_item">
									<?php $this->equipment_item( $abprf_infos ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function equipment_item( $abprf_infos, $field = array(), $key = '' ) {
				$key          = $key ?: '';
				$field        = $field ?: array();
				$price_type   = array_key_exists( 'price_type', $abprf_infos ) ? $abprf_infos['price_type'] : 'hourly';
				$icon_image   = array_key_exists( 'icon', $field ) ? $field['icon'] : '';
				$name         = array_key_exists( 'name', $field ) ? $field['name'] : '';
				$brand         = array_key_exists( 'brand', $field ) ? $field['brand'] : '';
				$qty          = array_key_exists( 'qty', $field ) ? $field['qty'] : '';
				$max_ty       = array_key_exists( 'max_qty', $field ) ? $field['max_qty'] : '';
				$hourly_rate  = array_key_exists( 'hourly_rate', $field ) ? $field['hourly_rate'] : '';
				$daily_rate   = array_key_exists( 'daily_rate', $field ) ? $field['daily_rate'] : '';
				$monthly_rate = array_key_exists( 'monthly_rate', $field ) ? $field['monthly_rate'] : '';
				$description  = array_key_exists( 'description', $field ) ? $field['description'] : '';
				?>
                <div class="abprf_delete_area equipment_item _setting_item ">
                    <input type="hidden" name="equipment_hidden_id[]" value="<?php echo esc_attr( $key ); ?>"/>
                    <div class="_f_equal">
                        <span class="_fs_label"><?php esc_html_e( 'Image or Icon', 'abprf-rental-forge' ); ?></span>
                        <div class="_fj_between">
							<?php do_action( 'abprf_add_image_icon', 'equipment_icon[]', $icon_image ); ?>
							<?php ABPRF_Layout::button_delete_sort(); ?>
                        </div>
                    </div>
                    <div class="_divider_xxs"></div>
                    <label class="_f_equal">
                        <span><?php esc_html_e( 'Equipment', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                        <input type="text" class="_form_control validation_name" name="equipment_name[]" placeholder="<?php esc_attr_e( 'EX: Bike', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $name ); ?>"/>
                    </label>
                    <div class="_divider_xxs"></div>
                    <label class="_f_equal">
                        <span><?php esc_html_e( 'Brand', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                        <input type="text" class="_form_control validation_name" name="equipment_brand[]" placeholder="<?php esc_attr_e( 'EX: Yamaha R15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $brand ); ?>"/>
                    </label>
                    <div class="_divider_xxs"></div>
                    <label class="_f_equal">
                        <span><?php esc_html_e( 'Quantity', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="equipment_qty[]" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $qty ); ?>"/>
                    </label>
                    <div class="_divider_xxs"></div>
                    <label class="_f_equal">
                        <span><?php esc_html_e( 'Max Quantity', 'abprf-rental-forge' ); ?></span>
                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="equipment_max_qty[]" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $max_ty ); ?>"/>
                    </label>
                    <div data-collapse="#hourly" class="<?php echo esc_attr( ( $price_type == 'hourly' || $price_type == 'hourly_daily' ) ? 'rf_active' : '' ); ?>">
                        <div class="_divider_xxs"></div>
                        <label class="_f_equal">
                            <span><?php esc_html_e( 'Hourly Rate', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                            <input type="text" class="_form_control validation_price" name="hourly_rate[]" placeholder="Ex: 10" value="<?php echo esc_attr( $hourly_rate ); ?>"/>
                        </label>
                    </div>
                    <div data-collapse="#daily" class="<?php echo esc_attr( ( $price_type == 'daily' || $price_type == 'hourly_daily' ) ? 'rf_active' : '' ); ?>">
                        <div class="_divider_xxs"></div>
                        <label class="_f_equal">
                            <span><?php esc_html_e( 'Daily  Rate', 'abprf-rental-forge' ); ?></span>
                            <input type="text" class="_form_control validation_price" name="daily_rate[]" placeholder="Ex: 10" value="<?php echo esc_attr( $daily_rate ); ?>"/>
                        </label>
                    </div>
                    <div data-collapse="#monthly" class="<?php echo esc_attr( $price_type == 'monthly' ? 'rf_active' : '' ); ?>">
                        <div class="_divider_xxs"></div>
                        <label class="_f_equal">
                            <span><?php esc_html_e( 'Monthly  Rate', 'abprf-rental-forge' ); ?></span>
                            <input type="text" class="_form_control validation_price" name="monthly_rate[]" placeholder="Ex: 10" value="<?php echo esc_attr( $monthly_rate ); ?>"/>
                        </label>
                    </div>
                    <div class="_divider_xxs"></div>
                    <label class="_f_equal">
                        <span><?php esc_html_e( 'Description', 'abprf-rental-forge' ); ?></span>
                        <textarea class="_form_control" name="equipment_description[]" placeholder="<?php esc_attr_e( 'EX: Description', 'abprf-rental-forge' ); ?>"><?php echo esc_html( $description ); ?></textarea>
                    </label>
                </div>
				<?php
			}
		}
		new ABPRF_EquipmentPrice();
	}