<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    if (!class_exists('ABPRF_Documentation')) {
        class ABPRF_Documentation {
            public function __construct() {
                add_action('abprf_load_documentation', array($this, 'load_documentation'));
            }
            public function load_documentation(): void {
                $label = ABPRF_Function::label();
                /* translators: %s: plugin label. */
                $doc_title = sprintf(__('Documentation — How to Use %s', 'abp-rentalforge'), $label);
                ?>
                <div class="abp_panel_max_1200_mar_auto abp_documentation">
                    <div class="_panel_head">
                        <h3 class="abp_gap_xs">📘<?php echo esc_html($doc_title); ?></h3>
                        <div class="abp_doc_head_links">
                            <a class="_btn_white_xs" href="https://demo-rf.abp-team.com/documentation/" target="_blank" rel="noopener"><?php esc_html_e('Full Documentation', 'abp-rentalforge'); ?> <i class="fas fa-book-open"></i></a>
                            <a class="_btn_white_xs" href="https://demo-rf.abp-team.com/" target="_blank" rel="noopener"><?php esc_html_e('Live Demo', 'abp-rentalforge'); ?> <i class="fas fa-desktop"></i></a>
                        </div>
                    </div>
                    <div class="_panel_body_fd_column_gap">
                        <div class="dash_card">
                            <div class="dash_card_body">
                                <p class="dash_body_lead"><?php esc_html_e('Set up and start renting in ~15 minutes.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('This guide walks you through the plugin step by step: what every menu does, in which order to set things up, how rental posts and properties work, and how daily deliveries and collections run your shop.', 'abp-rentalforge'); ?></p>
                            </div>
                        </div>
                        <?php if (!defined('ABPRF_DIR_PRO')) { ?>
                            <div class="dash_card doc_pro_banner">
                                <div class="dash_card_body">
                                    <p class="doc_pro_kicker"><?php esc_html_e('NEED MORE POWER?', 'abp-rentalforge'); ?></p>
                                    <p class="doc_pro_title"><?php esc_html_e('Upgrade to RentalForge Pro', 'abp-rentalforge'); ?></p>
                                    <p class="doc_pro_text"><?php esc_html_e('PDF invoices, CSV order export, automatic order emails, discounts & seasonal pricing, partial payments, and an advanced deposit system.', 'abp-rentalforge'); ?></p>
                                    <div>
                                        <a class="doc_pro_btn" href="https://abp-team.com/downloads/abp-rentalforge-pro/" target="_blank" rel="noopener"><?php esc_html_e('Buy Now', 'abp-rentalforge'); ?> <i class="fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🚀</span> <?php esc_html_e('Quick Start — Follow these steps in order', 'abp-rentalforge'); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <div class="doc_grid">
                                    <?php
                                        $this->step_card(
                                            '1',
                                            __('Check Dashboard', 'abp-rentalforge'),
                                            __('Verify everything is ready.', 'abp-rentalforge'),
                                            __('The Dashboard shows your plugin, WordPress, PHP and WooCommerce versions. Everything green means you can continue. If WooCommerce is missing, install it from the System Status section — it is required for rental payments. The same section creates your Booking, List, Property and Gallery pages, and can import demo posts to explore safely.', 'abp-rentalforge'),
                                            'dashboard',
                                            __('Open Dashboard', 'abp-rentalforge')
                                        );
                                        $this->step_card(
                                            '2',
                                            __('Global Configuration ON/OFF', 'abp-rentalforge'),
                                            __('Decide which features you need.', 'abp-rentalforge'),
                                            __('Global Configuration → ON/OFF turns features on/off for the whole site (Brand, Deposit, Features, Additional Services, Client Info, FAQ, Terms, Category, Location…). The defaults work out of the box, so you can skip this step.', 'abp-rentalforge'),
                                            'configuration',
                                            __('Open Global Configuration', 'abp-rentalforge'),
                                            ['configuration' => 'on_off']
                                        );
                                        $this->step_card(
                                            '3',
                                            __('Create Global Lists', 'abp-rentalforge'),
                                            __('Build the reusable lists every rental shares.', 'abp-rentalforge'),
                                            __('The Global Configuration tab holds the shared reusable lists — Dates, Location, and (if enabled) Category, Brand, Feature, Additional Services, Client Forms, and FAQ/Terms resources. Create them once — they appear as dropdowns when creating a rental post.', 'abp-rentalforge'),
                                            'configuration',
                                            __('Open Global Configuration', 'abp-rentalforge'),
                                            ['configuration' => 'location']
                                        );
                                        $this->step_card(
                                            '4',
                                            __('Create a Rental Post', 'abp-rentalforge'),
                                            __('Add your first rentable item type.', 'abp-rentalforge'),
                                            __('Press the + button, then add a title, image and description, pick a rent rule (hourly, daily, multi-day, monthly), set the schedule, category, location and brand. A post is the bookable listing customers see — e.g. Motor Bike, Car, Bicycle.', 'abp-rentalforge'),
                                            'posts',
                                            __('Open Post Lists', 'abp-rentalforge')
                                        );
                                        $this->step_card(
                                            '5',
                                            __('Add Properties', 'abp-rentalforge'),
                                            __('Add the individual units customers actually rent.', 'abp-rentalforge'),
                                            __('A property is one rentable unit — e.g. Ducati Panigale V4 under Motor Bike. Each property has its own stock quantity, reserved quantity, price per rent rule, deposit, gallery and features. Paid orders reduce this stock automatically.', 'abp-rentalforge'),
                                            'properties',
                                            __('Open Properties', 'abp-rentalforge')
                                        );
                                        $this->step_card(
                                            '6',
                                            __('Add Shortcodes & Pages', 'abp-rentalforge'),
                                            __('Publish the booking and listing pages.', 'abp-rentalforge'),
                                            __('The Dashboard creates Booking ([abprf-booking]), List ([abprf-post]), Property ([abprf-property]) and Gallery ([abprf-gallery]) pages for you via System Status. Put the list page in your menu so customers can browse everything; you can also insert the shortcodes into any page.', 'abp-rentalforge'),
                                            'dashboard',
                                            __('Open Dashboard', 'abp-rentalforge')
                                        );
                                        $this->step_card(
                                            '7',
                                            __('Test & Go Live', 'abp-rentalforge'),
                                            __('Try it with demo data, then watch the orders.', 'abp-rentalforge'),
                                            __('New to the plugin? Import the built-in demo data from the Dashboard’s System Status section to explore everything safely. When live, every purchase appears under Orders, and the Dashboard’s Deliveries and Collections sections run your daily handovers and returns.', 'abp-rentalforge'),
                                            'orders',
                                            __('Open Orders', 'abp-rentalforge')
                                        );
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🔛</span> <?php esc_html_e('The ON/OFF tab — what it is and why it matters', 'abp-rentalforge'); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text"><?php esc_html_e('Global Configuration → ON/OFF is the master control panel of the plugin. Every feature is listed there with a simple switch. Each switch controls a whole feature area of the site — not just one post.', 'abp-rentalforge'); ?></p>
                                <div class="doc_grid">
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">🟢 <?php esc_html_e('Switch ON (default)', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text"><?php esc_html_e('The feature is active everywhere. You see its menus, its global lists, and its fields on the rental post page.', 'abp-rentalforge'); ?></p>
                                        </div>
                                    </div>
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">⚪ <?php esc_html_e('Switch OFF', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text"><?php esc_html_e('The feature is completely hidden from the whole site — its menus, forms, and fields disappear. This keeps your admin screen clean and shows only what you use.', 'abp-rentalforge'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <p class="dash_body_lead"><?php esc_html_e('Popular switches you may want to change:', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Brand', 'abp-rentalforge'); ?></b> — <?php esc_html_e('turn OFF if your items have no brands to show.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Deposit', 'abp-rentalforge'); ?></b> — <?php esc_html_e('turn OFF if you never take security deposits.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Additional Services', 'abp-rentalforge'); ?></b> — <?php esc_html_e('turn OFF if you have no extras (helmets, insurance, delivery) to sell.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Client Info (Name / Address / Phone / Email)', 'abp-rentalforge'); ?></b> — <?php esc_html_e('turn OFF the fields you do not need to collect at checkout.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Category / Location', 'abp-rentalforge'); ?></b> — <?php esc_html_e('turn OFF the ones you do not use to hide their menus and post fields.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('FAQ / Terms & Conditions', 'abp-rentalforge'); ?></b> — <?php esc_html_e('turn OFF if you do not need these blocks on the rental page.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><b><?php esc_html_e('Tip:', 'abp-rentalforge'); ?></b> <?php esc_html_e('You can change the switches at any time. Turning a feature back ON restores its menus and post fields instantly, so feel free to experiment.', 'abp-rentalforge'); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🏍️</span> <?php esc_html_e('Rental Post vs Property — made clear', 'abp-rentalforge'); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text"><?php esc_html_e('Every rentable thing has two levels: the listing customers browse, and the physical units they take home.', 'abp-rentalforge'); ?></p>
                                <div class="doc_grid">
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">📋 <?php esc_html_e('Rental Post (the listing)', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text"><?php esc_html_e('The bookable item type — e.g. Motor Bike, Car, Bicycle, Drone. It holds the title, images, description, rent rule, schedule, template, category, location and brand. Customers find and open posts; they never rent a post directly.', 'abp-rentalforge'); ?></p>
                                        </div>
                                    </div>
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">🔑 <?php esc_html_e('Property (the unit)', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text"><?php esc_html_e('The individual unit that goes out the door — e.g. Ducati Panigale V4, BMC, Bugatti Chiron. Each property has its own stock quantity, reserved quantity, min/max per order, price for every rent rule, deposit, gallery and features. Paid orders reduce this stock automatically.', 'abp-rentalforge'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <p class="dash_body_lead">💰 <?php esc_html_e('Where do prices go?', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('Prices, quantities, reserved stock and min/max per order are set per property, separately for every rent rule (hourly price, daily price…). The rental post itself holds no price — it only decides which rent rule applies.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_lead">⏱️ <?php esc_html_e('How are rent rules charged?', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Hourly Rate', 'abp-rentalforge'); ?></b> — <?php esc_html_e('hourly price × hours × quantity. Best for bikes, tools, hourly equipment.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Daily Rate', 'abp-rentalforge'); ?></b> — <?php esc_html_e('daily price × days × quantity. Best for bicycles, apartments, day hires.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Daily & Hourly Rate', 'abp-rentalforge'); ?></b> — <?php esc_html_e('full days at the daily rate plus extra hours at the hourly rate. Best for cars and premium vehicles.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Monthly / Monthly & Daily Rate', 'abp-rentalforge'); ?></b> — <?php esc_html_e('same idea for long-term hires, priced per month.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('The duration is calculated automatically from the customer’s start and end date/time.', 'abp-rentalforge'); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🗂️</span> <?php esc_html_e('Every menu explained', 'abp-rentalforge'); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text">• <b><?php echo esc_html($label); ?><?php esc_html_e('List', 'abp-rentalforge'); ?></b> — <?php esc_html_e('all your rental posts: create, edit, clone, trash, restore, and permanently delete them.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Properties', 'abp-rentalforge'); ?></b> — <?php esc_html_e('all individual units across every post, with stock, prices and deposits.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Orders', 'abp-rentalforge'); ?></b> — <?php esc_html_e('every customer booking with rent period, property info, prices, status, payment and billing details. You can cancel an item from here.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Global Configuration', 'abp-rentalforge'); ?></b> — <?php esc_html_e('one tab for all global settings and shared reusable lists: general (label, slug, booked status…), the ON/OFF switches, colors, fonts and CSS, plus Dates, Locations and the optional Category, Brand and Feature lists (see the sub-tabs list below).', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Dashboard', 'abp-rentalforge'); ?></b> — <?php esc_html_e('revenue and rental KPIs, today’s deliveries and collections, recent orders, and a System Status health check with quick tools: install WooCommerce, create pages, and import or add demo data.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Documentation', 'abp-rentalforge'); ?></b> — <?php esc_html_e('this guide.', 'abp-rentalforge'); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">⚡</span> <?php esc_html_e('RentalForge Pro — advanced features', 'abp-rentalforge'); ?></h4>
                                <?php if (!defined('ABPRF_DIR_PRO')) { ?>
                                    <a class="doc_pro_btn" href="https://abp-team.com/downloads/abp-rentalforge-pro/" target="_blank" rel="noopener"><?php esc_html_e('Get RentalForge Pro', 'abp-rentalforge'); ?></a>
                                <?php } ?>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text"><?php esc_html_e('Everything in the free plugin is included in RentalForge Pro, plus these advanced features:', 'abp-rentalforge'); ?></p>
                                <div class="doc_grid">
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">💸 <?php esc_html_e('Pricing & Discounts', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Seasonal Pricing & Discount Rules', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Early Bird Pricing & Discount', 'abp-rentalforge'); ?></p>
                                        </div>
                                    </div>
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">💳 <?php esc_html_e('Payments', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Partial Payment (deposit now, balance later)', 'abp-rentalforge'); ?></p>
                                        </div>
                                    </div>
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">📍 <?php esc_html_e('Inventory & Operations', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Location-wise Stock Management', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Check-in Management', 'abp-rentalforge'); ?></p>
                                        </div>
                                    </div>
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">🧾 <?php esc_html_e('Documents & Automation', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Export Orders to PDF & CSV', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Professional PDF Invoice Generation', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Automatically Send PDF Invoices by Email', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Resend Booking Emails with PDF Attachments', 'abp-rentalforge'); ?></p>
                                        </div>
                                    </div>
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">🛠️ <?php esc_html_e('Admin & Support', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Create Bookings Directly from the Admin Dashboard', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Advanced Email Notifications', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Premium Support', 'abp-rentalforge'); ?></p>
                                            <p class="dash_body_text">• <?php esc_html_e('Priority Updates & New Features', 'abp-rentalforge'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">📦</span> <?php esc_html_e('Order List & My Account bookings', 'abp-rentalforge'); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_lead">🗂️ <?php esc_html_e('Order List (admin → Orders)', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('The Orders tab lists every booking placed on your site. Use the Order Filter to narrow it down — by post, booking date or date range, order date or range, customer, location, order ID, or billing name / e-mail / phone. Press Search to apply, or More Options to reveal the extra fields.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('Status tabs above the table instantly switch the list and show a live count: All Booking, Booking Completed, and each WooCommerce order status (Processing, Completed, On hold…).', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('Each row shows the order and date, the rent period, the booked property info, the client information, the prices, the rent status, the payment method and the billing details. From the Action column you can cancel an item.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('With RentalForge Pro active you also get 📄 Download Booking PDF and 📧 Re-send Mail on every row, plus the Order Lists PDF / Order Lists CSV export buttons at the top (CSV needs no PDF add-on; the PDF export requires the ABP PDF Tools add-on).', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_lead">👤 <?php esc_html_e('My Account bookings (customer frontend)', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('RentalForge adds a dedicated "My Bookings" tab to the WooCommerce My Account page, right after Orders. Each logged-in customer sees only their own bookings, shown with the same responsive rental design as the site.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('Every booking card shows the rental title, order ID and date, the rent period with its status badge (Waiting, In Rent, Completed, Delay…), the duration and rate rule, the location, the booked items, the additional services and client information (when enabled), a full price breakdown (Rent, Additional, Deposit, Total), the payment method and the order status.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('The list is paginated, and with the Pro plugin + ABP PDF Tools an extra Download PDF button is shown on each booking.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('Customers reach it from their account menu or directly at yoursite.com/my-account/abprf-bookings/. Guests are asked to log in first.', 'abp-rentalforge'); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🌍</span> <?php esc_html_e('Inside Global Configuration', 'abp-rentalforge'); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text">• <b><?php esc_html_e('Dates', 'abp-rentalforge'); ?></b> — <?php esc_html_e('global date and time formatting, sale buffer before/after bookings, and how far ahead customers can book.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Location', 'abp-rentalforge'); ?></b> — <?php esc_html_e('reusable pickup/area list used by every post and order filter.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Category / Brand', 'abp-rentalforge'); ?></b> — <?php esc_html_e('optional classifications; they only appear here when switched ON in Configuration.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Features', 'abp-rentalforge'); ?></b> — <?php esc_html_e('highlight badges shown on the rental page (e.g. GPS Included, Full Insurance, Free Helmet).', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Additional Services', 'abp-rentalforge'); ?></b> — <?php esc_html_e('optional add-ons with their own prices (helmet, insurance, delivery).', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Client Form', 'abp-rentalforge'); ?></b> — <?php esc_html_e('renter fields collected at checkout (name, address, phone, email…).', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Resources', 'abp-rentalforge'); ?></b> — <?php esc_html_e('reusable FAQ and Terms & Conditions blocks.', 'abp-rentalforge'); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🔄</span> <?php esc_html_e('Daily workflow — deliveries & collections', 'abp-rentalforge'); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text"><?php esc_html_e('The Dashboard runs your shop floor. Every booking carries a rent status that moves forward as the item goes out and comes back:', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Waiting', 'abp-rentalforge'); ?></b> — <?php esc_html_e('order paid, item not handed over yet.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('In Rent', 'abp-rentalforge'); ?></b> — <?php esc_html_e('item is out with the customer.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e('Completed', 'abp-rentalforge'); ?></b> — <?php esc_html_e('item returned and checked in.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_lead">🚚 <?php esc_html_e('Today’s Deliveries', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('One row per post with everything starting today: booked quantity, available and reserved stock, plus today’s total rent and deposit. Click a row to see who booked what — customer names, quantities, all prices, additional services and deposits — then press Start Rent on a booking to move it into rent.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_lead">📦 <?php esc_html_e('Today’s Collections', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text"><?php esc_html_e('One row per order due back: customer, items, rent period, order total, deposit, and how overdue it is when the time has passed. Press Mark Received when the item is back in your hands.', 'abp-rentalforge'); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🔑</span> <?php esc_html_e('Shortcodes you can place anywhere', 'abp-rentalforge'); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text">• <code>[abprf-booking]</code> — <?php esc_html_e('full rental listing with search, filters and the booking flow. Ideal for your Booking page.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <code>[abprf-post]</code> — <?php esc_html_e('rental listing without the booking wrapper. Ideal for your List page.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <code>[abprf-property]</code> — <?php esc_html_e('shows individual properties with galleries. Ideal for your Property page.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <code>[abprf-gallery]</code> — <?php esc_html_e('displays rental images in a gallery or slider.', 'abp-rentalforge'); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">✅</span> <?php esc_html_e('Good to know', 'abp-rentalforge'); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text">• <?php esc_html_e('The booked order status (Global Configuration → General → Booked Status) decides when stock is deducted from inventory. Set it to the statuses you consider “paid”.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <?php esc_html_e('Customers pick a start and end date/time from the schedule shown on the rental page before choosing properties and quantities.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <?php esc_html_e('You can configure global additional services and client forms once, then use them on any post.', 'abp-rentalforge'); ?></p>
                                <p class="dash_body_text">• <?php esc_html_e('Deactivating the plugin keeps your posts, properties and orders safe — nothing is deleted, so you can update or reinstall freely.', 'abp-rentalforge'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            private function step_card(string $step, string $title, string $lead, string $text, string $tab = '', string $btn_label = '', array $extra_args = []): void {
                $url = $tab ? ABPRF_Function::build_url($tab, $extra_args) : '';
                ?>
                <div class="dash_card">
                    <div class="dash_card_head">
                        <h4><span class="dash_step"><?php echo esc_html($step); ?></span><?php echo esc_html($title); ?></h4>
                        <?php if ($url && $btn_label) { ?>
                            <a class="_btn_light_active_xs" href="<?php echo esc_url($url); ?>"><?php echo esc_html($btn_label); ?></a>
                        <?php } ?>
                    </div>
                    <div class="dash_card_body">
                        <p class="dash_body_lead"><?php echo esc_html($lead); ?></p>
                        <p class="dash_body_text"><?php echo esc_html($text); ?></p>
                    </div>
                </div>
                <?php
            }
        }
        new ABPRF_Documentation();
    }
