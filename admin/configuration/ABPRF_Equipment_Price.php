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
				$infos               = array_key_exists( 'equipment_infos', $abprf_infos ) ? $abprf_infos['equipment_infos'] : [];
				?>
                <div class="tab_item abprf_equipment_price" data-tabs="#abprf_equipment_price">
                    <h4 class="_abprf_color_theme"><span class="<?php echo esc_attr( $equipment_icon ); ?> _mar_r_xs"></span> <?php echo esc_html( $post_title . ' ' . __( ' : ', 'abprf-rental-forge' ) . ' ' . __( 'Equipment and Price Configuration', 'abprf-rental-forge' ) ); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="abprf_equipment_configuration abprf_configuration_content">
                        <div class="_ov_auto">
                            <table class="_abprf">
                                <thead>
                                <tr>
                                    <th class="_w_125"><?php esc_html_e( 'Icon / Image', 'abprf-rental-forge' ); ?></th>
                                    <th class="_min_200"><?php esc_html_e( 'Name', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></th>
                                    <th class="_min_100"><?php esc_html_e( 'Quantity', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></th>
                                    <th class="_min_100"><?php esc_html_e( 'Max qty', 'abprf-rental-forge' ); ?></th>
                                    <th class="_min_100"><?php esc_html_e( 'Hourly Price', 'abprf-rental-forge' ); ?></th>
                                    <th class="_min_100"><?php esc_html_e( 'Daily Price', 'abprf-rental-forge' ); ?></th>
                                    <th class="_min_100"><?php esc_html_e( 'Weekly Price', 'abprf-rental-forge' ); ?></th>
                                    <th class="_min_100"><?php esc_html_e( 'Monthly Price', 'abprf-rental-forge' ); ?></th>
                                    <th class="_min_250"><?php esc_html_e( 'Description', 'abprf-rental-forge' ); ?></th>
                                    <th class="_w_75"><?php esc_html_e( 'Action', 'abprf-rental-forge' ); ?></th>
                                </tr>
                                </thead>
                                <tbody class="abprf_insert_item abprf_sortable">
								<?php
									if ( sizeof( $infos ) > 0 ) {
										foreach ( $infos as $key => $info ) {
											$this->equipment_item( $info, $key );
										}
									}
								?>
                                </tbody>
                            </table>
                        </div>
                        <div class="_divider"></div>
						<?php ABPRF_LIB_Layout::button_add( __( 'Add New Equipment', 'abprf-rental-forge' ) ); ?>
                        <div class="abprf_d_none">
                            <table class="_abprf">
                                <tbody class="abprf_hidden_item">
								<?php $this->equipment_item(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function equipment_item( $field = array(), $key = '' ): void {
				$key           = $key ?: '';
				$field         = $field ?: array();
				$icon_image    = array_key_exists( 'icon', $field ) ? $field['icon'] : '';
				$name          = array_key_exists( 'name', $field ) ? $field['name'] : '';
				$qty           = array_key_exists( 'qty', $field ) ? $field['qty'] : '';
				$max_ty        = array_key_exists( 'max_qty', $field ) ? $field['max_qty'] : '';
				$hourly_price  = array_key_exists( 'hourly_price', $field ) ? $field['hourly_price'] : '';
				$weekly_price  = array_key_exists( 'weekly_price', $field ) ? $field['weekly_price'] : '';
				$daily_price   = array_key_exists( 'daily_price', $field ) ? $field['daily_price'] : '';
				$monthly_price = array_key_exists( 'monthly_price', $field ) ? $field['monthly_price'] : '';
				$description   = array_key_exists( 'description', $field ) ? $field['description'] : '';
				$icon          = $image = "";
				if ( $icon_image ) {
					if ( preg_match( '/\s/', $icon_image ) ) {
						$icon = $icon_image;
					} else {
						$image = $icon_image;
					}
				}
				?>
                <tr class="abprf_delete_area ">
                    <td> <?php do_action( 'abprf_add_image_icon', 'equipment_icon[]', $icon, $image ); ?>  </td>
                    <td>
                        <input type="hidden" name="equipment_hidden_id[]" value="<?php echo esc_attr( $key ); ?>"/>
                        <label>
                            <input type="text" class="_form_control validation_name" name="equipment_name[]" placeholder="<?php esc_attr_e( 'EX: Adult', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $name ); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="equipment_qty[]" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $qty ); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="equipment_max_qty[]" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $max_ty ); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" class="_form_control validation_price" name="hourly_price[]" placeholder="Ex: 10" value="<?php echo esc_attr( $hourly_price ); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" class="_form_control validation_price" name="daily_price[]" placeholder="Ex: 10" value="<?php echo esc_attr( $daily_price ); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" class="_form_control validation_price" name="weekly_price[]" placeholder="Ex: 10" value="<?php echo esc_attr( $weekly_price ); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="text" class="_form_control validation_price" name="monthly_price[]" placeholder="Ex: 10" value="<?php echo esc_attr( $monthly_price ); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <textarea class="_form_control" name="equipment_description[]" placeholder="<?php esc_attr_e( 'EX: Description', 'abprf-rental-forge' ); ?>"><?php echo esc_html( $description ); ?></textarea>
                        </label>
                    </td>
                    <td><?php ABPRF_LIB_Layout::button_delete_sort(); ?></td>
                </tr>
				<?php
			}
		}
		new ABPRF_EquipmentPrice();
	}