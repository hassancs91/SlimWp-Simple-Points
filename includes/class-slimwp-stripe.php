<?php
if (!defined('ABSPATH')) {
    exit;
}

class SlimWP_Stripe {
    
    private $points_system;
    private $settings;
    
    public function __construct($points_system) {
        $this->points_system = $points_system;
        $this->settings = get_option('slimwp_stripe_settings', array());
        
        // Initialize hooks
        add_action('init', array($this, 'init'));
        add_action('wp_ajax_slimwp_create_checkout_session', array($this, 'create_checkout_session'));
        add_action('wp_ajax_nopriv_slimwp_create_checkout_session', array($this, 'create_checkout_session'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Webhook handler
        add_action('wp_ajax_slimwp_stripe_webhook', array($this, 'handle_webhook'));
        add_action('wp_ajax_nopriv_slimwp_stripe_webhook', array($this, 'handle_webhook'));
        
        // Shortcode
        add_shortcode('slimwp_stripe_packages', array($this, 'packages_shortcode'));
        
        // Return URLs
        add_action('template_redirect', array($this, 'handle_return_urls'));
    }
    
    public function init() {
        // Create database tables
        SlimWP_Stripe_Database::create_tables();
    }
    
    public function enqueue_scripts() {
        if (!$this->is_enabled()) {
            return;
        }
        
        // Enqueue Stripe.js
        wp_enqueue_script(
            'stripe-js',
            'https://js.stripe.com/v3/',
            array(),
            null,
            true
        );
        
        wp_enqueue_script(
            'slimwp-stripe-checkout',
            SLIMWP_PLUGIN_URL . 'includes/assets/js/stripe-checkout.js',
            array('jquery', 'stripe-js'),
            SLIMWP_VERSION,
            true
        );
        
        wp_enqueue_style(
            'slimwp-stripe-frontend',
            SLIMWP_PLUGIN_URL . 'includes/assets/css/stripe-frontend.css',
            array(),
            SLIMWP_VERSION
        );
        
        wp_localize_script('slimwp-stripe-checkout', 'slimwp_stripe', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('slimwp_stripe_nonce'),
            'publishable_key' => $this->get_publishable_key(),
            'loading_text' => __('Processing...', 'SlimWp-Simple-Points'),
            'error_text' => __('An error occurred. Please try again.', 'SlimWp-Simple-Points')
        ));
    }
    
    public function packages_shortcode($atts) {
        if (!$this->is_enabled()) {
            return '<p>' . __('Stripe integration is currently disabled.', 'SlimWp-Simple-Points') . '</p>';
        }
        
        $atts = shortcode_atts(array(
            'columns' => 3,
            'show_description' => 'yes'
        ), $atts);
        
        $packages = SlimWP_Stripe_Database::get_packages('active');
        
        if (empty($packages)) {
            return '<p>' . __('No packages available at the moment.', 'SlimWp-Simple-Points') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="slimwp-stripe-packages" data-columns="<?php echo esc_attr($atts['columns']); ?>">
            <?php foreach ($packages as $package): ?>
                <div class="slimwp-package-card">
                    <div class="package-header">
                        <h3 class="package-name"><?php echo esc_html($package->name); ?></h3>
                        <div class="package-price">
                            <span class="currency"><?php echo esc_html($package->currency); ?></span>
                            <span class="amount"><?php echo esc_html(number_format($package->price, 2)); ?></span>
                        </div>
                    </div>
                    
                    <div class="package-content">
                        <div class="package-points">
                            <span class="points-amount"><?php echo esc_html(number_format($package->points)); ?></span>
                            <span class="points-label"><?php _e('Points', 'SlimWp-Simple-Points'); ?></span>
                        </div>
                        
                        <?php if ($atts['show_description'] === 'yes' && !empty($package->description)): ?>
                            <p class="package-description"><?php echo esc_html($package->description); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="package-footer">
                        <?php if (is_user_logged_in()): ?>
                            <button class="slimwp-buy-button" 
                                    data-package-id="<?php echo esc_attr($package->id); ?>"
                                    data-package-name="<?php echo esc_attr($package->name); ?>">
                                <?php _e('Buy Now', 'SlimWp-Simple-Points'); ?>
                            </button>
                        <?php else: ?>
                            <a href="<?php echo wp_login_url(get_permalink()); ?>" class="slimwp-login-button">
                                <?php _e('Login to Purchase', 'SlimWp-Simple-Points'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function create_checkout_session() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'slimwp_stripe_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!is_user_logged_in()) {
            wp_send_json_error('User must be logged in');
        }
        
        $package_id = intval($_POST['package_id']);
        $package = SlimWP_Stripe_Database::get_package($package_id);
        
        if (!$package || $package->status !== 'active') {
            wp_send_json_error('Invalid package');
        }
        
        try {
            $this->load_stripe_library();
            \Stripe\Stripe::setApiKey($this->get_secret_key());
            
            $user_id = get_current_user_id();
            $user = get_userdata($user_id);
            
            // Create checkout session
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($package->currency),
                        'product_data' => [
                            'name' => $package->name,
                            'description' => sprintf(
                                __('%s points for %s', 'SlimWp-Simple-Points'),
                                number_format($package->points),
                                get_bloginfo('name')
                            ),
                        ],
                        'unit_amount' => intval($package->price * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => add_query_arg(array(
                    'slimwp_stripe_success' => '1',
                    'session_id' => '{CHECKOUT_SESSION_ID}'
                ), home_url()),
                'cancel_url' => add_query_arg(array(
                    'slimwp_stripe_cancel' => '1'
                ), home_url()),
                'customer_email' => $user->user_email,
                'metadata' => [
                    'user_id' => $user_id,
                    'package_id' => $package_id,
                    'points' => $package->points,
                    'site_url' => home_url()
                ]
            ]);
            
            // Store pending purchase
            SlimWP_Stripe_Database::create_purchase(array(
                'user_id' => $user_id,
                'package_id' => $package_id,
                'stripe_session_id' => $session->id,
                'stripe_payment_intent_id' => '',
                'amount_paid' => $package->price,
                'currency' => $package->currency,
                'points_awarded' => $package->points,
                'status' => 'pending'
            ));
            
            wp_send_json_success(array(
                'session_id' => $session->id
            ));
            
        } catch (Exception $e) {
            error_log('SlimWP Stripe Error: ' . $e->getMessage());
            wp_send_json_error('Payment processing error');
        }
    }
    
    public function handle_webhook() {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $endpoint_secret = $this->get_webhook_secret();
        
        if (empty($endpoint_secret)) {
            http_response_code(400);
            exit('Webhook secret not configured');
        }
        
        try {
            $this->load_stripe_library();
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            http_response_code(400);
            exit('Invalid payload');
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            http_response_code(400);
            exit('Invalid signature');
        }
        
        // Handle the event
        switch ($event['type']) {
            case 'checkout.session.completed':
                $this->handle_successful_payment($event['data']['object']);
                break;
            case 'checkout.session.expired':
                $this->handle_expired_session($event['data']['object']);
                break;
            default:
                error_log('SlimWP Stripe: Unhandled event type ' . $event['type']);
        }
        
        http_response_code(200);
        exit('OK');
    }
    
    private function handle_successful_payment($session) {
        $session_id = $session['id'];
        $payment_intent_id = $session['payment_intent'] ?? '';
        
        // Get purchase record
        $purchase = SlimWP_Stripe_Database::get_purchase_by_session($session_id);
        
        if (!$purchase || $purchase->status === 'completed') {
            return; // Already processed or not found
        }
        
        // Update purchase status
        SlimWP_Stripe_Database::update_purchase_status($session_id, 'completed', $payment_intent_id);
        
        // Award points to permanent balance
        $result = $this->points_system->add_points(
            $purchase->user_id,
            $purchase->points_awarded,
            sprintf(__('Purchased %s package', 'SlimWp-Simple-Points'), $purchase->package_name ?? 'points'),
            'stripe_purchase',
            'permanent'
        );
        
        if (!is_wp_error($result)) {
            // Send confirmation email
            $this->send_purchase_confirmation_email($purchase);
            
            error_log("SlimWP Stripe: Successfully awarded {$purchase->points_awarded} points to user {$purchase->user_id}");
        } else {
            error_log("SlimWP Stripe: Failed to award points - " . $result->get_error_message());
        }
    }
    
    private function handle_expired_session($session) {
        $session_id = $session['id'];
        SlimWP_Stripe_Database::update_purchase_status($session_id, 'expired');
    }
    
    public function handle_return_urls() {
        if (isset($_GET['slimwp_stripe_success'])) {
            $this->handle_success_return();
        } elseif (isset($_GET['slimwp_stripe_cancel'])) {
            $this->handle_cancel_return();
        }
    }
    
    private function handle_success_return() {
        $session_id = sanitize_text_field($_GET['session_id'] ?? '');
        
        if (empty($session_id)) {
            return;
        }
        
        $purchase = SlimWP_Stripe_Database::get_purchase_by_session($session_id);
        
        if ($purchase && $purchase->status === 'completed') {
            // Show success message
            add_action('wp_footer', function() use ($purchase) {
                echo '<div class="slimwp-stripe-success-notice">';
                echo '<p>' . sprintf(
                    __('Thank you! Your purchase was successful. %s points have been added to your account.', 'SlimWp-Simple-Points'),
                    number_format($purchase->points_awarded)
                ) . '</p>';
                echo '</div>';
            });
        }
    }
    
    private function handle_cancel_return() {
        add_action('wp_footer', function() {
            echo '<div class="slimwp-stripe-cancel-notice">';
            echo '<p>' . __('Your purchase was cancelled. No charges were made.', 'SlimWp-Simple-Points') . '</p>';
            echo '</div>';
        });
    }
    
    private function send_purchase_confirmation_email($purchase) {
        $template = $this->get_email_template();
        $user = get_userdata($purchase->user_id);
        
        if (!$user || empty($template)) {
            return;
        }
        
        $subject = sprintf(__('[%s] Points Purchase Confirmation', 'SlimWp-Simple-Points'), get_bloginfo('name'));
        
        $message = str_replace(
            array(
                '{user_name}',
                '{points}',
                '{amount}',
                '{currency}',
                '{site_name}',
                '{site_url}'
            ),
            array(
                $user->display_name,
                number_format($purchase->points_awarded),
                number_format($purchase->amount_paid, 2),
                $purchase->currency,
                get_bloginfo('name'),
                home_url()
            ),
            $template
        );
        
        wp_mail($user->user_email, $subject, $message);
    }
    
    private function load_stripe_library() {
        if (!class_exists('\Stripe\Stripe')) {
            require_once SLIMWP_PLUGIN_DIR . 'includes/stripe-php/init.php';
        }
    }
    
    private function is_enabled() {
        return !empty($this->settings['enabled']) && $this->settings['enabled'] === '1';
    }
    
    private function get_publishable_key() {
        $mode = $this->settings['mode'] ?? 'test';
        return $this->settings[$mode . '_publishable_key'] ?? '';
    }
    
    private function get_secret_key() {
        $mode = $this->settings['mode'] ?? 'test';
        return $this->settings[$mode . '_secret_key'] ?? '';
    }
    
    private function get_webhook_secret() {
        $mode = $this->settings['mode'] ?? 'test';
        return $this->settings[$mode . '_webhook_secret'] ?? '';
    }
    
    private function get_email_template() {
        $default_template = "Hello {user_name},\n\nThank you for your purchase!\n\nYou have successfully purchased {points} points for {amount} {currency}.\n\nThe points have been added to your permanent balance and are ready to use.\n\nBest regards,\n{site_name}\n{site_url}";
        
        return $this->settings['email_template'] ?? $default_template;
    }
    
    public function get_webhook_url() {
        return admin_url('admin-ajax.php?action=slimwp_stripe_webhook');
    }
}
