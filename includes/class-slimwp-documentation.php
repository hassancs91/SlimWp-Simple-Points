<?php
if (!defined('ABSPATH')) {
    exit;
}

class SlimWP_Documentation {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_documentation_menu'), 25);
    }
    
    public function add_documentation_menu() {
        add_submenu_page(
            'slimwp-points',
            __('Documentation', 'SlimWp-Simple-Points'),
            __('Documentation', 'SlimWp-Simple-Points'),
            'manage_options',
            'slimwp-points-documentation',
            array($this, 'documentation_page')
        );
    }
    
    public function documentation_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('SlimWP Simple Points - Documentation', 'SlimWp-Simple-Points'); ?></h1>
            
            <div style="max-width: 1200px;">
                
                <!-- Navigation Tabs -->
                <nav class="nav-tab-wrapper" style="margin-bottom: 20px;">
                    <a href="#overview" class="nav-tab nav-tab-active" onclick="showTab('overview')"><?php _e('Overview', 'SlimWp-Simple-Points'); ?></a>
                    <a href="#balance-system" class="nav-tab" onclick="showTab('balance-system')"><?php _e('Balance System', 'SlimWp-Simple-Points'); ?></a>
                    <a href="#woocommerce" class="nav-tab" onclick="showTab('woocommerce')"><?php _e('WooCommerce', 'SlimWp-Simple-Points'); ?></a>
                    <a href="#stripe" class="nav-tab" onclick="showTab('stripe')"><?php _e('Stripe', 'SlimWp-Simple-Points'); ?></a>
                    <a href="#shortcodes" class="nav-tab" onclick="showTab('shortcodes')"><?php _e('Shortcodes', 'SlimWp-Simple-Points'); ?></a>
                    <a href="#developer" class="nav-tab" onclick="showTab('developer')"><?php _e('Developer API', 'SlimWp-Simple-Points'); ?></a>
                </nav>
                
                <!-- Overview Tab -->
                <div id="overview" class="tab-content">
                    <?php $this->render_overview_section(); ?>
                </div>
                
                <!-- Balance System Tab -->
                <div id="balance-system" class="tab-content" style="display:none;">
                    <?php $this->render_balance_system_section(); ?>
                </div>
                
                <!-- WooCommerce Tab -->
                <div id="woocommerce" class="tab-content" style="display:none;">
                    <?php $this->render_woocommerce_section(); ?>
                </div>
                
                <!-- Stripe Tab -->
                <div id="stripe" class="tab-content" style="display:none;">
                    <?php $this->render_stripe_section(); ?>
                </div>
                
                <!-- Shortcodes Tab -->
                <div id="shortcodes" class="tab-content" style="display:none;">
                    <?php $this->render_shortcodes_section(); ?>
                </div>
                
                <!-- Developer API Tab -->
                <div id="developer" class="tab-content" style="display:none;">
                    <?php $this->render_developer_section(); ?>
                </div>
                
            </div>
        </div>
        
        <style>
        .tab-content {
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
        }
        .doc-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            border-left: 4px solid #0073aa;
        }
        .doc-section h3 {
            margin-top: 0;
            color: #0073aa;
        }
        .doc-code {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 13px;
            line-height: 1.5;
            overflow-x: auto;
            margin: 10px 0;
        }
        .doc-list {
            margin: 15px 0;
            padding-left: 20px;
        }
        .doc-list li {
            margin-bottom: 8px;
        }
        .doc-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .doc-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .doc-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0;
        }
        </style>
        
        <script>
        function showTab(tabName) {
            // Hide all tab contents
            var contents = document.querySelectorAll('.tab-content');
            contents.forEach(function(content) {
                content.style.display = 'none';
            });
            
            // Remove active class from all tabs
            var tabs = document.querySelectorAll('.nav-tab');
            tabs.forEach(function(tab) {
                tab.classList.remove('nav-tab-active');
            });
            
            // Show selected tab content
            document.getElementById(tabName).style.display = 'block';
            
            // Add active class to clicked tab
            event.target.classList.add('nav-tab-active');
        }
        </script>
        <?php
    }
    
    private function render_overview_section() {
        ?>
        <div class="doc-section">
            <h3><?php _e('Welcome to SlimWP Simple Points', 'SlimWp-Simple-Points'); ?></h3>
            <p><?php _e('SlimWP Simple Points is a lightweight dual-balance points system for WordPress that allows you to reward users with both temporary and permanent points.', 'SlimWp-Simple-Points'); ?></p>
            
            <h4><?php _e('Key Features', 'SlimWp-Simple-Points'); ?></h4>
            <ul class="doc-list">
                <li><strong><?php _e('Dual Balance System:', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Free Balance and Permanent Balance for different use cases', 'SlimWp-Simple-Points'); ?></li>
                <li><strong><?php _e('WooCommerce Integration:', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Award points for digital product purchases', 'SlimWp-Simple-Points'); ?></li>
                <li><strong><?php _e('Stripe Integration:', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Allow users to purchase points packages', 'SlimWp-Simple-Points'); ?></li>
                <li><strong><?php _e('Flexible Hooks:', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Registration bonuses, daily login rewards, and scheduled resets', 'SlimWp-Simple-Points'); ?></li>
                <li><strong><?php _e('Shortcodes:', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Easy display of points and purchase options', 'SlimWp-Simple-Points'); ?></li>
                <li><strong><?php _e('Developer API:', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Full programmatic control for custom implementations', 'SlimWp-Simple-Points'); ?></li>
            </ul>
            
            <div class="doc-info">
                <strong><?php _e('Getting Started:', 'SlimWp-Simple-Points'); ?></strong><br>
                <?php _e('1. Configure your point rewards in Settings', 'SlimWp-Simple-Points'); ?><br>
                <?php _e('2. Set up integrations (WooCommerce/Stripe) if needed', 'SlimWp-Simple-Points'); ?><br>
                <?php _e('3. Use shortcodes to display points on your site', 'SlimWp-Simple-Points'); ?><br>
                <?php _e('4. Monitor user activity in the Points Dashboard', 'SlimWp-Simple-Points'); ?>
            </div>
        </div>
        <?php
    }
    
    private function render_balance_system_section() {
        ?>
        <div class="doc-section">
            <h3><?php _e('Understanding the Dual Balance System', 'SlimWp-Simple-Points'); ?></h3>
            <p><?php _e('SlimWP Simple Points uses two separate balance types to give you maximum flexibility in how you reward users.', 'SlimWp-Simple-Points'); ?></p>
            
            <h4><?php _e('Free Balance', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-info">
                <strong><?php _e('Best for:', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Daily rewards, login bonuses, temporary promotions, trial periods', 'SlimWp-Simple-Points'); ?><br>
                <strong><?php _e('Features:', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Can be reset daily/monthly, temporary rewards, promotional campaigns', 'SlimWp-Simple-Points'); ?>
            </div>
            
            <h4><?php _e('Permanent Balance', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-success">
                <strong><?php _e('Best for:', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Purchases, achievements, referral rewards, one-time bonuses', 'SlimWp-Simple-Points'); ?><br>
                <strong><?php _e('Features:', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Never automatically reset, secure for valuable rewards, persistent value', 'SlimWp-Simple-Points'); ?>
            </div>
            
            <h4><?php _e('Balance Reset Functionality', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-warning">
                <strong><?php _e('Important:', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Balance resets only affect the Free Balance. Permanent Balance is never reset.', 'SlimWp-Simple-Points'); ?><br>
                <strong><?php _e('Warning:', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Reset operations REPLACE the user\'s free balance, they don\'t add to it.', 'SlimWp-Simple-Points'); ?>
            </div>
            
            <h4><?php _e('Usage Examples', 'SlimWp-Simple-Points'); ?></h4>
            <ul class="doc-list">
                <li><strong><?php _e('Daily Login (Free):', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Give 10 points daily, reset monthly to 100', 'SlimWp-Simple-Points'); ?></li>
                <li><strong><?php _e('Purchase Reward (Permanent):', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Award 500 permanent points for buying a course', 'SlimWp-Simple-Points'); ?></li>
                <li><strong><?php _e('Achievement (Permanent):', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Give 1000 points for completing a milestone', 'SlimWp-Simple-Points'); ?></li>
                <li><strong><?php _e('Promotional (Free):', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Weekly contest with temporary rewards', 'SlimWp-Simple-Points'); ?></li>
            </ul>
        </div>
        <?php
    }
    
    private function render_woocommerce_section() {
        ?>
        <div class="doc-section">
            <h3><?php _e('WooCommerce Integration Guide', 'SlimWp-Simple-Points'); ?></h3>
            
            <?php
            $wc_status = slimwp_get_woocommerce_status_message();
            if ($wc_status['status'] !== 'active'):
            ?>
                <div class="doc-warning">
                    <strong><?php _e('Requirements Not Met:', 'SlimWp-Simple-Points'); ?></strong><br>
                    <?php echo esc_html($wc_status['message']); ?><br>
                    <strong><?php _e('Action Required:', 'SlimWp-Simple-Points'); ?></strong> <?php echo esc_html($wc_status['action']); ?>
                </div>
            <?php else: ?>
                <div class="doc-success">
                    <?php echo esc_html($wc_status['message']); ?> <?php echo esc_html($wc_status['action']); ?>
                </div>
            <?php endif; ?>
            
            <h4><?php _e('How WooCommerce Integration Works', 'SlimWp-Simple-Points'); ?></h4>
            <ol class="doc-list">
                <li><?php _e('Add a "SlimWP Points" tab to digital product edit pages', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Set points amount per product', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Points awarded when order status changes to "completed"', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Points go to customer\'s permanent balance', 'SlimWp-Simple-Points'); ?></li>
            </ol>
            
            <h4><?php _e('Setup Instructions', 'SlimWp-Simple-Points'); ?></h4>
            <ol class="doc-list">
                <li><?php _e('Go to SlimWP Points → Settings', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Scroll to "WooCommerce Integration" section', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Enable the integration toggle', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Set your default points amount', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Choose when to award points (Processing vs Completed)', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Save settings', 'SlimWp-Simple-Points'); ?></li>
            </ol>
            
            <h4><?php _e('Configuring Products', 'SlimWp-Simple-Points'); ?></h4>
            <ol class="doc-list">
                <li><?php _e('Edit any digital (virtual) product in WooCommerce', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Look for the "SlimWP Points" tab in the product data section', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Check "Award Points" to enable points for this product', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Set the points amount (defaults to your global setting)', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Optionally customize the transaction description', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Save the product', 'SlimWp-Simple-Points'); ?></li>
            </ol>
            
            <h4><?php _e('Order Status Guide', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-info">
                <strong><?php _e('Processing (Immediate):', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Points awarded immediately after purchase (automatic)', 'SlimWp-Simple-Points'); ?><br>
                <strong><?php _e('Completed (Manual):', 'SlimWp-Simple-Points'); ?></strong> <?php _e('Points awarded when you manually complete the order (manual control)', 'SlimWp-Simple-Points'); ?>
            </div>
            
            <div class="doc-warning">
                <strong><?php _e('Important Notes:', 'SlimWp-Simple-Points'); ?></strong><br>
                • <?php _e('Only works with digital (virtual) products', 'SlimWp-Simple-Points'); ?><br>
                • <?php _e('Points are only awarded once per order', 'SlimWp-Simple-Points'); ?><br>
                • <?php _e('Guest purchases (users not logged in) do not receive points', 'SlimWp-Simple-Points'); ?><br>
                • <?php _e('Points go to the customer\'s permanent balance', 'SlimWp-Simple-Points'); ?>
            </div>
        </div>
        <?php
    }
    
    private function render_stripe_section() {
        ?>
        <div class="doc-section">
            <h3><?php _e('Stripe Integration Guide', 'SlimWp-Simple-Points'); ?></h3>
            
            <h4><?php _e('How Stripe Integration Works', 'SlimWp-Simple-Points'); ?></h4>
            <ul class="doc-list">
                <li><?php _e('Users purchase points packages via secure Stripe checkout', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Points are automatically added to permanent balance', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Use shortcode [slimwp_stripe_packages] to display packages', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Webhook verification ensures secure transactions', 'SlimWp-Simple-Points'); ?></li>
            </ul>
            
            <h4><?php _e('Setup Instructions', 'SlimWp-Simple-Points'); ?></h4>
            <ol class="doc-list">
                <li><?php _e('Create a Stripe account at stripe.com', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Get your API keys from the Stripe Dashboard', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Go to SlimWP Points → Settings → Stripe Integration', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Enable Stripe integration', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Enter your test API keys', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Set up webhooks (see instructions below)', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Test thoroughly in test mode', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Switch to live mode when ready', 'SlimWp-Simple-Points'); ?></li>
            </ol>
            
            <h4><?php _e('Webhook Setup Instructions', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-info">
                <strong><?php _e('Webhook URL:', 'SlimWp-Simple-Points'); ?></strong><br>
                <code><?php echo home_url('/wp-json/slimwp/v1/stripe/webhook'); ?></code>
            </div>
            
            <ol class="doc-list">
                <li><?php _e('Go to your Stripe Dashboard → Webhooks', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Click "Add endpoint"', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Paste the webhook URL above', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Select events: checkout.session.completed and checkout.session.expired', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Copy the webhook secret and paste it in the API keys section', 'SlimWp-Simple-Points'); ?></li>
                <li><?php _e('Save your settings', 'SlimWp-Simple-Points'); ?></li>
            </ol>
            
            <h4><?php _e('Email Template Placeholders', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
{user_name} - <?php _e('Customer\'s display name', 'SlimWp-Simple-Points'); ?><br>
{points} - <?php _e('Points purchased', 'SlimWp-Simple-Points'); ?><br>
{amount} - <?php _e('Amount paid', 'SlimWp-Simple-Points'); ?><br>
{currency} - <?php _e('Currency code', 'SlimWp-Simple-Points'); ?><br>
{site_name} - <?php _e('Your site name', 'SlimWp-Simple-Points'); ?><br>
{site_url} - <?php _e('Your site URL', 'SlimWp-Simple-Points'); ?>
            </div>
            
            <div class="doc-warning">
                <strong><?php _e('Important Security Notes:', 'SlimWp-Simple-Points'); ?></strong><br>
                • <?php _e('Always test thoroughly in test mode before switching to live mode', 'SlimWp-Simple-Points'); ?><br>
                • <?php _e('Keep your API keys secure and never share them', 'SlimWp-Simple-Points'); ?><br>
                • <?php _e('Webhook secret is required for security verification', 'SlimWp-Simple-Points'); ?><br>
                • <?php _e('Monitor your Stripe dashboard for any unusual activity', 'SlimWp-Simple-Points'); ?>
            </div>
        </div>
        <?php
    }
    
    private function render_shortcodes_section() {
        ?>
        <div class="doc-section">
            <h3><?php _e('Available Shortcodes', 'SlimWp-Simple-Points'); ?></h3>
            
            <h4><?php _e('User Points Display', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
[slimwp_user_points] - <?php _e('Show both balances for current user', 'SlimWp-Simple-Points'); ?><br>
[slimwp_user_points type="free"] - <?php _e('Show only free balance', 'SlimWp-Simple-Points'); ?><br>
[slimwp_user_points type="permanent"] - <?php _e('Show only permanent balance', 'SlimWp-Simple-Points'); ?><br>
[slimwp_user_points type="total"] - <?php _e('Show combined total', 'SlimWp-Simple-Points'); ?>
            </div>
            
            <h4><?php _e('Points History', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
[slimwp_points_history] - <?php _e('Show user\'s transaction history', 'SlimWp-Simple-Points'); ?><br>
[slimwp_points_history limit="10"] - <?php _e('Limit to 10 most recent transactions', 'SlimWp-Simple-Points'); ?>
            </div>
            
            <h4><?php _e('Leaderboard', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
[slimwp_leaderboard] - <?php _e('Show top users by total points', 'SlimWp-Simple-Points'); ?><br>
[slimwp_leaderboard type="permanent"] - <?php _e('Show top users by permanent balance', 'SlimWp-Simple-Points'); ?><br>
[slimwp_leaderboard limit="5"] - <?php _e('Show top 5 users only', 'SlimWp-Simple-Points'); ?>
            </div>
            
            <h4><?php _e('Stripe Packages (if enabled)', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
[slimwp_stripe_packages] - <?php _e('Display available points packages for purchase', 'SlimWp-Simple-Points'); ?>
            </div>
            
            <h4><?php _e('Usage Examples', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-info">
                <strong><?php _e('In Posts/Pages:', 'SlimWp-Simple-Points'); ?></strong><br>
                <?php _e('You can add any shortcode directly in the WordPress editor.', 'SlimWp-Simple-Points'); ?><br><br>
                
                <strong><?php _e('In Themes/Templates:', 'SlimWp-Simple-Points'); ?></strong><br>
                <code>&lt;?php echo do_shortcode('[slimwp_user_points]'); ?&gt;</code><br><br>
                
                <strong><?php _e('In Widgets:', 'SlimWp-Simple-Points'); ?></strong><br>
                <?php _e('Add a Text widget and include any shortcode.', 'SlimWp-Simple-Points'); ?>
            </div>
        </div>
        <?php
    }
    
    private function render_developer_section() {
        ?>
        <div class="doc-section">
            <h3><?php _e('Developer API Reference', 'SlimWp-Simple-Points'); ?></h3>
            
            <h4><?php _e('Getting Points Instance', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
$points = SlimWP_Points::get_instance();
            </div>
            
            <h4><?php _e('Getting User Balances', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
// Get both balances
$balances = $points->get_user_points($user_id);
// Returns: array('free' => 150, 'permanent' => 500)

// Get specific balance
$free_balance = $points->get_user_points($user_id, 'free');
$permanent_balance = $points->get_user_points($user_id, 'permanent');

// Get total points
$total = $points->get_user_points($user_id, 'total');
            </div>
            
            <h4><?php _e('Adding Points', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
// Add to free balance
$result = $points->add_points($user_id, 100, 'Daily bonus', 'manual', 'free');

// Add to permanent balance  
$result = $points->add_points($user_id, 500, 'Purchase reward', 'purchase', 'permanent');

// Check if successful
if (!is_wp_error($result)) {
    echo 'Points added successfully!';
}
            </div>
            
            <h4><?php _e('Subtracting Points', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
// Subtract from free balance
$result = $points->subtract_points($user_id, 50, 'Used points', 'usage', 'free');

// Subtract from permanent balance
$result = $points->subtract_points($user_id, 200, 'Redemption', 'redemption', 'permanent');
            </div>
            
            <h4><?php _e('Setting Specific Balance', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
// Set free balance to specific amount
$result = $points->set_user_points($user_id, 1000, 'Monthly reset', 'reset', 'free');

// Set permanent balance to specific amount
$result = $points->set_user_points($user_id, 2500, 'Admin adjustment', 'adjustment', 'permanent');
            </div>
            
            <h4><?php _e('Helper Functions', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
// Check if user has enough points
if ($points->has_sufficient_points($user_id, 100, 'free')) {
    // User has at least 100 free points
}

// Get user's transaction history
$history = $points->get_user_history($user_id, 10); // Last 10 transactions

// Get leaderboard
$leaderboard = $points->get_leaderboard('total', 10); // Top 10 by total points
            </div>
            
            <h4><?php _e('Available Hooks', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
// After points are added
add_action('slimwp_points_added', function($user_id, $amount, $new_balance, $balance_type) {
    // Your custom code here
}, 10, 4);

// After points are subtracted  
add_action('slimwp_points_subtracted', function($user_id, $amount, $new_balance, $balance_type) {
    // Your custom code here
}, 10, 4);

// After balance is set
add_action('slimwp_points_balance_set', function($user_id, $new_balance, $balance_type) {
    // Your custom code here
}, 10, 3);

// WooCommerce points awarded
add_action('slimwp_woocommerce_points_awarded', function($user_id, $points, $order_id, $products) {
    // Your custom code here
}, 10, 4);
            </div>
            
            <h4><?php _e('Error Handling', 'SlimWp-Simple-Points'); ?></h4>
            <div class="doc-code">
$result = $points->add_points($user_id, 100, 'Test', 'manual', 'free');

if (is_wp_error($result)) {
    $error_message = $result->get_error_message();
    echo 'Error: ' . $error_message;
} else {
    echo 'Success! New balance: ' . $result;
}
            </div>
            
            <div class="doc-warning">
                <strong><?php _e('Important Notes:', 'SlimWp-Simple-Points'); ?></strong><br>
                • <?php _e('Always check if functions return WP_Error objects', 'SlimWp-Simple-Points'); ?><br>
                • <?php _e('User ID must be valid and exist in WordPress', 'SlimWp-Simple-Points'); ?><br>
                • <?php _e('Points amounts should be positive integers', 'SlimWp-Simple-Points'); ?><br>
                • <?php _e('Balance types must be "free", "permanent", or "total"', 'SlimWp-Simple-Points'); ?>
            </div>
        </div>
        <?php
    }
}