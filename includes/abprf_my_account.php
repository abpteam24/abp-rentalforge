<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    if (!class_exists('ABPRF_My_Account')) {
        class ABPRF_My_Account {
            const ENDPOINT = 'abprf-bookings';
            public function __construct() {
                add_action('init', array($this, 'register_endpoint'));
                add_filter('woocommerce_get_query_vars', array($this, 'query_vars'));
                add_filter('woocommerce_account_menu_items', array($this, 'menu_items'), 20);
                add_action('woocommerce_account_' . self::ENDPOINT . '_endpoint', array($this, 'render_bookings'));
                add_action('admin_init', array($this, 'maybe_flush_rules'));
            }
            public function register_endpoint(): void {
                add_rewrite_endpoint(self::ENDPOINT, EP_ROOT | EP_PAGES);
            }
            public function query_vars($vars) {
                $vars[self::ENDPOINT] = self::ENDPOINT;
                return $vars;
            }
            public function menu_items($items) {
                $slug = self::ENDPOINT;
                $menu = array();
                foreach ($items as $key => $value) {
                    $menu[$key] = $value;
                    if ('orders' === $key && !isset($menu[$slug])) {
                        $menu[$slug] = __('My Bookings', 'abp-rentalforge');
                    }
                }
                if (!isset($menu[$slug])) {
                    $menu[$slug] = __('My Bookings', 'abp-rentalforge');
                }
                return $menu;
            }
            public function maybe_flush_rules(): void {
                if (!get_option('abprf_my_account_flushed')) {
                    $this->register_endpoint();
                    flush_rewrite_rules();
                    update_option('abprf_my_account_flushed', 1);
                }
            }
            public function render_bookings(): void {
                $user_id = get_current_user_id();
                ?>
                <div class="abprf_area abprf_my_account">
                    <style>
						.abprf_my_account ._section_card {border: 1px solid var(--rf_color_border);border-radius: var(--rf_br);padding: var(--rf_gap_xs);}
						.abprf_my_account ul.abp li {display: -webkit-flex;display: flex;-webkit-flex-wrap: wrap;flex-wrap: wrap;gap: var(--rf_gap_xxs);-webkit-align-items: baseline;align-items: baseline;}
						.abprf_my_account ul.abp li strong {min-width: 150px;display: inline-block;}
						.abprf_my_account .abp_tag {background: var(--rf_color_light);border: 1px solid var(--rf_color_border);border-radius: 50px;color: var(--rf_color_default);font-size: var(--rf_fs_small);padding: var(--rf_gap_xxs) var(--rf_gap_xs);white-space: nowrap;}
						.abprf_my_account ._pagination {display: -webkit-flex;display: flex;gap: var(--rf_gap_xxs);-webkit-flex-wrap: wrap;flex-wrap: wrap;}
						.abprf_my_account ._page_link {min-width: 36px;height: 36px;border: 1px solid var(--rf_color_border);background: var(--rf_color_white);color: var(--rf_color_default);border-radius: var(--rf_br);display: -webkit-inline-flex;display: inline-flex;-webkit-align-items: center;align-items: center;-webkit-justify-content: center;justify-content: center;text-decoration: none;padding: 0 var(--rf_gap_xs);}
						.abprf_my_account ._page_link:hover {border-color: var(--rf_color_theme);color: var(--rf_color_theme);}
						.abprf_my_account ._page_active {background: var(--rf_color_theme);border-color: var(--rf_color_theme);color: var(--rf_color_white);}
                    </style>
                    <h4><?php echo esc_html__('My Bookings', 'abp-rentalforge'); ?></h4>
                    <div class="_divider_xs"></div>
                    <?php
                        if (!$user_id) {
                            echo '<p>' . esc_html__('Please login to see your bookings.', 'abp-rentalforge') . '</p>';
                            return;
                        }
                        $limit = absint(ABPRF_Function::get_option('abprf_per_page_item', 20));
                        if ($limit < 1) {
                            $limit = 20;
                        }
                        $page = isset($_GET['abprf_page']) ? absint($_GET['abprf_page']) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                        if ($page < 1) {
                            $page = 1;
                        }
                        $offset = ($page - 1) * $limit;
                        $filters = array(
                            'user_id' => $user_id,
                            'status' => 'all',
                            'order_by' => 'created_at',
                            'order_dir' => 'DESC',
                        );
                        $total = ABPRF_Query::get_booking_query($filters, 0, 0, true);
                        $bookings = ABPRF_Query::get_booking_query($filters, $limit, $offset);
                        if (!empty($bookings) && is_array($bookings)) {
                            foreach ($bookings as $booking_item) {
                                $this->booking_card($booking_item);
                            }
                            $this->pagination($total, $limit, $page);
                        } else {
                            echo '<p>' . esc_html__('You have no bookings yet.', 'abp-rentalforge') . '</p>';
                        }
                    ?>
                </div>
                <?php
            }
            private function booking_card($booking_item): void {
                $post_id = absint($booking_item['post_id'] ?? 0);
                $order_id = $booking_item['order_id'] ?? '';
                $order_status = $booking_item['order_status'] ?? '';
                $book_status = $booking_item['book_status'] ?? '';
                $created_at = $booking_item['created_at'] ?? '';
                $start_time = $booking_item['start_time'] ?? '';
                $end_time = $booking_item['end_time'] ?? '';
                $location = $booking_item['location'] ?? '';
                $price_info = json_decode($booking_item['price_info'] ?? '', true) ?: array();
                $total = (float)($price_info['item_total'] ?? 0);
                $rent = (float)($price_info['rent'] ?? 0);
                $ex_price = (float)($price_info['ex_price'] ?? 0);
                $deposit = (float)($price_info['deposit'] ?? 0);
                $ticket_infos = json_decode($booking_item['property_info'] ?? '', true) ?: array();
                $additional_infos = json_decode($booking_item['ex_info'] ?? '', true) ?: array();
                $passenger_infos = json_decode($booking_item['pass_info'] ?? '', true) ?: array();
                $others = json_decode($booking_item['others'] ?? '', true) ?: array();
                $duration = $others['duration'] ?? '';
                $rent_rule = $others['rent_rule'] ?? '';
                $end_time_format = (!empty($start_time) && !empty($end_time) && gmdate('Y-m-d', strtotime($start_time)) === gmdate('Y-m-d', strtotime($end_time))) ? 'time' : 'full';
                $book_status = ABPRF_Layout::get_book_status($order_id, $start_time, $end_time, $book_status);
                ?>
                <div class="_section_card _mar_b">
                    <div class="_fj_between">
                        <div>
                            <h5 style="margin:0 0 4px;"><?php ABPRF_Layout::title($post_id); ?></h5>
                            <p style="margin:0;" class="abp_color_gray">#<?php echo esc_html($order_id); ?> - <?php echo esc_html(ABPRF_Function::date_format($created_at)); ?></p>
                        </div>
                        <span class="abp_tag"><?php echo esc_html(ABPRF_Layout::status_text($order_status)); ?></span>
                    </div>
                    <div class="_divider_xs"></div>
                    <ul class="abp">
                        <li>
                            <strong><?php esc_html_e('Rent Time', 'abp-rentalforge'); ?></strong>
                            <?php echo esc_html(ABPRF_Function::date_format($start_time, 'full') . ' - ' . ABPRF_Function::date_format($end_time, $end_time_format)); ?>
                            <span class="abp_tag"><?php echo esc_html(ABPRF_Layout::book_status_text($book_status)); ?></span>
                        </li>
                        <?php if (!empty($duration) || !empty($rent_rule)) { ?>
                            <li>
                                <strong><?php esc_html_e('Duration', 'abp-rentalforge'); ?></strong> <?php echo esc_html($duration); ?>
                                <?php if (!empty($rent_rule)) { ?><span class="abp_tag"><?php echo esc_html(ABPRF_Layout::rent_rules($rent_rule)); ?></span><?php } ?>
                            </li>
                        <?php } ?>
                        <?php if (ABPRF_Function::on_off('location') && !empty($location)) { ?>
                            <li><strong><?php esc_html_e('Location', 'abp-rentalforge'); ?></strong> <?php echo esc_html(ABPRF_Function::location_value($location)); ?></li>
                        <?php } ?>
                        <?php if (!empty($booking_item['payment_method'])) { ?>
                            <li><strong><?php esc_html_e('Payment Method', 'abp-rentalforge'); ?></strong> <?php echo esc_html($booking_item['payment_method']); ?></li>
                        <?php } ?>
                    </ul>
                    <?php if (!empty($ticket_infos)) { ?>
                        <div class="_divider_xs"></div>
                        <?php ABPRF_Layout::ticket_info($ticket_infos); ?>
                    <?php } ?>
                    <?php if (ABPRF_Function::on_off('additional_info') && !empty($additional_infos)) { ?>
                        <div class="_divider_xs"></div>
                        <?php ABPRF_Layout::additional_info($additional_infos); ?>
                    <?php } ?>
                    <?php if (ABPRF_Function::on_off('client_info') && !empty($passenger_infos)) { ?>
                        <div class="_divider_xs"></div>
                        <?php ABPRF_Layout::client_info($passenger_infos); ?>
                    <?php } ?>
                    <div class="_divider_xs"></div>
                    <ul class="abp">
                        <li><strong><?php esc_html_e('Rent', 'abp-rentalforge'); ?></strong> : <?php echo $rent > 0 ? wp_kses_post(wc_price($rent)) : esc_html__('FREE', 'abp-rentalforge'); ?></li>
                        <?php if (ABPRF_Function::on_off('additional_info') && $ex_price > 0) { ?>
                            <li><strong><?php esc_html_e('Additional', 'abp-rentalforge'); ?></strong> : <?php echo wp_kses_post(wc_price($ex_price)); ?></li>
                        <?php } ?>
                        <?php if (ABPRF_Function::on_off('deposit') && $deposit > 0) { ?>
                            <li><strong><?php esc_html_e('Deposit', 'abp-rentalforge'); ?></strong> : <?php echo wp_kses_post(wc_price($deposit)); ?></li>
                        <?php } ?>
                        <li><strong><?php esc_html_e('Total', 'abp-rentalforge'); ?></strong> : <?php echo $total > 0 ? wp_kses_post(wc_price($total)) : esc_html__('FREE', 'abp-rentalforge'); ?></li>
                    </ul>
                    <?php do_action('abprf_my_account_partial_payment', $booking_item); ?>
                    <div class="_divider_xs"></div>
                    <div class="_fj_start _f_wrap_gap_xxs">
                        <?php do_action('abprf_my_account_booking_actions', $booking_item); ?>
                    </div>
                </div>
                <?php
            }
            private function pagination($total, $limit, $page): void {
                if (empty($limit) || (int)$total <= (int)$limit) {
                    return;
                }
                $pages = (int)ceil((int)$total / (int)$limit);
                if ($pages < 2) {
                    return;
                }
                $base = wc_get_account_endpoint_url(self::ENDPOINT);
                ?>
                <div class="_fj_center _mar_b">
                    <div class="_pagination">
                        <?php for ($i = 1; $i <= $pages; $i++) {
                            $is_current = ($i === (int)$page);
                            $url = (1 === $i) ? $base : add_query_arg('abprf_page', $i, $base); ?>
                            <?php if ($is_current) { ?>
                                <span class="_page_link _page_active"><?php echo esc_html($i); ?></span>
                            <?php } else { ?>
                                <a class="_page_link" href="<?php echo esc_url($url); ?>"><?php echo esc_html($i); ?></a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <?php
            }
        }
        new ABPRF_My_Account();
    }
