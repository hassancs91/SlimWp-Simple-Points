<?php
if (!defined('ABSPATH')) {
    exit;
}

class SlimWP_Settings {
    
    private $points_system;
    private $hooks_option;
    
    public function __construct($points_system) {
        $this->points_system = $points_system;
        $this->hooks_option = $points_system->get_hooks_option();
        
        add_action('admin_menu', array($this, 'add_settings_menu'), 20);
    }
    
    public function add_settings_menu() {
        add_submenu_page(
            'slimwp-points',
            __('Settings', 'slimwp'),
            __('Settings', 'slimwp'),
            'manage_options',
            'slimwp-points-settings',
            array($this, 'settings_page')
        );
    }
    
    public function settings_page() {
         if (isset($_POST['submit']) && check_admin_referer('slimwp_settings')) {
            $hooks = array(
                'register' => isset($_POST['hooks']['register']),
                'register_points' => intval($_POST['register_points']),
                'register_balance_type' => sanitize_text_field($_POST['register_balance_type']),
                'daily_login' => isset($_POST['hooks']['daily_login']),
                'daily_login_points' => intval($_POST['daily_login_points']),
                'daily_login_balance_type' => sanitize_text_field($_POST['daily_login_balance_type']),
                'daily_reset' => isset($_POST['hooks']['daily_reset']),
                'daily_reset_points' => intval($_POST['daily_reset_points']),
                'monthly_reset' => isset($_POST['hooks']['monthly_reset']),
                'monthly_reset_points' => intval($_POST['monthly_reset_points'])
            );
            update_option($this->hooks_option, $hooks);
            echo '<div class="notice notice-success is-dismissible" style="margin: 20px 20px 0;"><p>✅ Settings saved successfully!</p></div>';
        }
        
        $hooks = get_option($this->hooks_option, array(
            'register' => true,
            'register_points' => 100,
            'register_balance_type' => 'permanent',
            'daily_login' => true,
            'daily_login_points' => 10,
            'daily_login_balance_type' => 'free',
            'daily_reset' => false,
            'daily_reset_points' => 100,
            'monthly_reset' => false,
            'monthly_reset_points' => 1000
        ));
        ?>
        <style>
            .wrap { margin: 0; }
            .slimwp-settings-wrap { background: #f0f0f1; min-height: 100vh; margin: 0; padding: 0; }
            .slimwp-settings-header { background: #fff; padding: 20px 32px; margin: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); position: relative; z-index: 10; }
            .slimwp-settings-header h1 { margin: 0; font-size: 24px; font-weight: 600; color: #1d2327; line-height: 1.3; }
            .slimwp-settings-content { padding: 32px 20px; }
            .settings-card { background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; }
            .settings-card h2 { margin: 0 0 24px; font-size: 18px; font-weight: 600; color: #1d2327; padding-bottom: 16px; border-bottom: 1px solid #e1e1e1; }
            .settings-row { display: flex; align-items: start; padding: 20px 0; border-bottom: 1px solid #f0f0f1; }
            .settings-row:last-child { border-bottom: none; }
            .settings-label { flex: 0 0 300px; padding-right: 32px; }
            .settings-label h3 { margin: 0 0 4px; font-size: 15px; font-weight: 600; color: #1d2327; }
            .settings-label p { margin: 0; font-size: 13px; color: #50575e; line-height: 1.5; }
            .settings-control { flex: 1; }
            .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
            .toggle-switch input { opacity: 0; width: 0; height: 0; }
            .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccd0d4; transition: .3s; border-radius: 24px; }
            .toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
            input:checked + .toggle-slider { background-color: #2271b1; }
            input:checked + .toggle-slider:before { transform: translateX(20px); }
            .points-input { margin-top: 12px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
            .points-input input { width: 80px; padding: 8px 12px; border: 1px solid #dcdcde; border-radius: 4px; font-size: 14px; }
            .points-input select { padding: 8px 12px; border: 1px solid #dcdcde; border-radius: 4px; font-size: 14px; }
            .points-input input:focus, .points-input select:focus { border-color: #2271b1; outline: none; box-shadow: 0 0 0 1px #2271b1; }
            .balance-type-selector { margin-top: 8px; }
            .usage-examples { background: #f6f7f7; border-radius: 8px; padding: 24px; margin-top: 16px; }
            .usage-examples h3 { margin: 0 0 16px; font-size: 16px; font-weight: 600; color: #1d2327; }
            .code-example { background: #1d2327; color: #fff; padding: 16px 20px; border-radius: 4px; margin-bottom: 16px; font-family: 'Monaco', 'Consolas', monospace; font-size: 13px; line-height: 1.6; overflow-x: auto; }
            .code-example strong { color: #72aee6; font-weight: normal; }
            .code-comment { color: #8b8b8b; }
            .submit-wrap { margin-top: 32px; padding-top: 24px; border-top: 1px solid #e1e1e1; }
            .button-primary { background: #2271b1; border-color: #2271b1; color: #fff; padding: 8px 24px; font-size: 14px; font-weight: 500; border-radius: 4px; transition: background 0.2s; }
            .button-primary:hover { background: #135e96; border-color: #135e96; }
            .api-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; }
            .api-card { background: #fff; border: 1px solid #e1e1e1; border-radius: 4px; padding: 16px; }
            .api-card h4 { margin: 0 0 8px; font-size: 14px; font-weight: 600; color: #1d2327; }
            .api-card code { background: #f0f0f1; padding: 2px 4px; border-radius: 2px; font-size: 12px; }
            .warning-box { background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 12px 16px; margin-top: 12px; font-size: 13px; color: #856404; }
            .warning-box strong { color: #856404; }
            .info-box { background: #e7f2fd; border: 1px solid #bee5eb; border-radius: 4px; padding: 12px 16px; margin-top: 12px; font-size: 13px; color: #004085; }
            .info-box strong { color: #004085; }
            
            /* Responsive Design */
            @media (max-width: 768px) {
                .slimwp-settings-header { padding: 16px 20px; }
                .slimwp-settings-header h1 { font-size: 20px; }
                .slimwp-settings-content { padding: 20px 16px; }
                .settings-card { padding: 20px; }
                .settings-card h2 { font-size: 16px; margin-bottom: 16px; padding-bottom: 12px; }
                .settings-row { flex-direction: column; padding: 16px 0; }
                .settings-label { flex: none; padding-right: 0; margin-bottom: 12px; }
                .points-input { flex-direction: column; align-items: flex-start; }
                .points-input input { width: 100px; }
                .api-grid { grid-template-columns: 1fr; }
                .usage-examples { padding: 16px; }
                .code-example { padding: 12px 16px; font-size: 12px; }
            }
        </style>
        
        <div class="wrap">
            <div class="slimwp-settings-wrap">
                <div class="slimwp-settings-header">
                    <h1>⚙️ SlimWP Points Settings</h1>
                </div>
                
                <div class="slimwp-settings-content">
                    <form method="post">
                        <?php wp_nonce_field('slimwp_settings'); ?>
                        
                        <div class="settings-card">
                            <h2>Automatic Points Rewards</h2>
                            
                            <div class="settings-row">
                                <div class="settings-label">
                                    <h3>Registration Bonus</h3>
                                    <p>Award points automatically when new users sign up</p>
                                </div>
                                <div class="settings-control">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="hooks[register]" value="1" <?php checked($hooks['register']); ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <div class="points-input">
                                        <span>Award</span>
                                        <input type="number" name="register_points" value="<?php echo $hooks['register_points']; ?>" min="0">
                                        <span>points to</span>
                                        <select name="register_balance_type">
                                            <option value="free" <?php selected($hooks['register_balance_type'], 'free'); ?>>Free Balance</option>
                                            <option value="permanent" <?php selected($hooks['register_balance_type'], 'permanent'); ?>>Permanent Balance</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="settings-row">
                                <div class="settings-label">
                                    <h3>Daily Login Bonus</h3>
                                    <p>Give points to users who log in each day (once per day)</p>
                                </div>
                                <div class="settings-control">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="hooks[daily_login]" value="1" <?php checked($hooks['daily_login']); ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <div class="points-input">
                                        <span>Award</span>
                                        <input type="number" name="daily_login_points" value="<?php echo $hooks['daily_login_points']; ?>" min="0">
                                        <span>points to</span>
                                        <select name="daily_login_balance_type">
                                            <option value="free" <?php selected($hooks['daily_login_balance_type'], 'free'); ?>>Free Balance</option>
                                            <option value="permanent" <?php selected($hooks['daily_login_balance_type'], 'permanent'); ?>>Permanent Balance</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="submit-wrap">
                                <?php submit_button('Save Settings', 'primary', 'submit', false); ?>
                            </div>
                        </div>
                        
                        <div class="settings-card">
                            <h2>Balance Reset Features</h2>
                            
                            <div class="info-box">
                                <strong>ℹ️ Note:</strong> Balance resets only affect the Free Balance. Permanent Balance is never reset.
                            </div>
                            
                            <div class="settings-row">
                                <div class="settings-label">
                                    <h3>Daily Balance Reset</h3>
                                    <p>Reset user's FREE balance to a fixed amount when they login each day</p>
                                </div>
                                <div class="settings-control">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="hooks[daily_reset]" value="1" <?php checked($hooks['daily_reset']); ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <div class="points-input">
                                        <span>Set free balance to</span>
                                        <input type="number" name="daily_reset_points" value="<?php echo $hooks['daily_reset_points']; ?>" min="0">
                                        <span>points on daily login</span>
                                    </div>
                                    <div class="warning-box">
                                        <strong>⚠️ Warning:</strong> This will REPLACE the user's free balance, not add to it. Permanent balance remains untouched.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="settings-row">
                                <div class="settings-label">
                                    <h3>Monthly Balance Reset</h3>
                                    <p>Reset user's FREE balance to a fixed amount on their first login each month</p>
                                </div>
                                <div class="settings-control">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="hooks[monthly_reset]" value="1" <?php checked($hooks['monthly_reset']); ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <div class="points-input">
                                        <span>Set free balance to</span>
                                        <input type="number" name="monthly_reset_points" value="<?php echo $hooks['monthly_reset_points']; ?>" min="0">
                                        <span>points on monthly login</span>
                                    </div>
                                    <div class="warning-box">
                                        <strong>⚠️ Warning:</strong> This will REPLACE the user's free balance, not add to it. Permanent balance remains untouched.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="submit-wrap">
                                <?php submit_button('Save Settings', 'primary', 'submit', false); ?>
                            </div>
                        </div>
                    </form>
                    
                    <div class="settings-card">
                        <h2>🚀 Developer Documentation</h2>
                        
                        <div class="usage-examples">
                            <h3>Quick Start Guide - Dual Balance System</h3>
                            
                            <div class="code-example">
<strong>// Get user's total balance (free + permanent)</strong>
$balance = slimwp()->get_balance($user_id);
<span class="code-comment">// or use the helper function</span>
$balance = slimwp_get_user_points($user_id);

<strong>// Get individual balances</strong>
$free_balance = slimwp()->get_free_balance($user_id);
$permanent_balance = slimwp()->get_permanent_balance($user_id);
                            </div>
                            
                            <div class="code-example">
<strong>// Add points to specific balance</strong>
<span class="code-comment">// Add to free balance (default)</span>
slimwp()->add_points($user_id, 50, 'Daily bonus', 'daily_login', 'free');

<span class="code-comment">// Add to permanent balance</span>
slimwp()->add_points($user_id, 100, 'Achievement unlocked', 'achievement', 'permanent');
                            </div>
                            
                            <div class="code-example">
<strong>// Subtract points (automatically deducts from free first, then permanent)</strong>
slimwp()->subtract_points($user_id, 75, 'Premium feature usage');
<span class="code-comment">// If user has 50 free + 100 permanent, this will:</span>
<span class="code-comment">// - Deduct 50 from free (leaving 0 free)</span>
<span class="code-comment">// - Deduct 25 from permanent (leaving 75 permanent)</span>
                            </div>
                            
                            <div class="code-example">
<strong>// Set specific balance (for resets)</strong>
<span class="code-comment">// Reset free balance only</span>
slimwp()->set_balance($user_id, 100, 'Daily reset', 'daily_reset', 'free');

<span class="code-comment">// Set permanent balance</span>
slimwp()->set_balance($user_id, 500, 'VIP upgrade', 'admin_adjustment', 'permanent');
                            </div>
                            
                            <div class="code-example">
<strong>// Check total balance before action</strong>
if (slimwp_get_user_points($user_id) >= 100) {
    <span class="code-comment">// User has enough total points, proceed</span>
    slimwp_subtract_user_points($user_id, 100, 'Unlocked premium content');
    <span class="code-comment">// System will automatically deduct from free first, then permanent</span>
}
                            </div>
                            
                            <div class="code-example">
<strong>// Helper functions (new SlimWP functions)</strong>
slimwp_get_user_points($user_id);              <span class="code-comment">// Returns total (free + permanent)</span>
slimwp_add_user_points($user_id, 50, 'Bonus'); <span class="code-comment">// Adds to free balance by default</span>
slimwp_subtract_user_points($user_id, 20, 'Used feature'); <span class="code-comment">// Deducts from free first</span>
                            </div>
                            
                            <div class="code-example">
<strong>// Hook into balance updates</strong>
add_action('slimwp_points_balance_updated', function($user_id, $amount, $new_total, $description) {
    <span class="code-comment">// $new_total is the combined balance (free + permanent)</span>
    $free = slimwp()->get_free_balance($user_id);
    $permanent = slimwp()->get_permanent_balance($user_id);
    
    <span class="code-comment">// Log or notify based on balance changes</span>
    error_log("User $user_id balance: Free=$free, Permanent=$permanent, Total=$new_total");
}, 10, 4);
                            </div>
                        </div>
                        
                        <h3 style="margin: 24px 0 16px; font-size: 16px; font-weight: 600;">Balance-Specific Functions</h3>
                        
                        <div class="api-grid">
                            <div class="api-card">
                                <h4>slimwp_get_user_free_points($user_id)</h4>
                                <p style="margin: 0; font-size: 13px; color: #50575e;">Get only the free balance (resettable)</p>
                            </div>
                            <div class="api-card">
                                <h4>slimwp_get_user_permanent_points($user_id)</h4>
                                <p style="margin: 0; font-size: 13px; color: #50575e;">Get only the permanent balance (never reset)</p>
                            </div>
                            <div class="api-card">
                                <h4>slimwp()->add_points($user_id, $amount, $desc, $type, $balance_type)</h4>
                                <p style="margin: 0; font-size: 13px; color: #50575e;">Add to specific balance. <code>$balance_type</code> can be 'free' or 'permanent'</p>
                            </div>
                            <div class="api-card">
                                <h4>slimwp()->set_balance($user_id, $amount, $desc, $type, $balance_type)</h4>
                                <p style="margin: 0; font-size: 13px; color: #50575e;">Set specific balance to exact amount</p>
                            </div>
                        </div>
                        
                        <h3 style="margin: 24px 0 16px; font-size: 16px; font-weight: 600;">Shortcodes</h3>
                        
                        <div class="api-grid">
                            <div class="api-card">
                                <h4>[slimwp_points]</h4>
                                <p style="margin: 0; font-size: 13px; color: #50575e;">Display total points balance</p>
                            </div>
                            <div class="api-card">
                                <h4>[slimwp_points_free]</h4>
                                <p style="margin: 0; font-size: 13px; color: #50575e;">Display free balance only</p>
                            </div>
                            <div class="api-card">
                                <h4>[slimwp_points_permanent]</h4>
                                <p style="margin: 0; font-size: 13px; color: #50575e;">Display permanent balance only</p>
                            </div>
                            <div class="api-card">
                                <h4>[slimwp_points type="free" user_id="123"]</h4>
                                <p style="margin: 0; font-size: 13px; color: #50575e;">Display specific balance for specific user</p>
                            </div>
                        </div>
                        
                        <h3 style="margin: 24px 0 16px; font-size: 16px; font-weight: 600;">Usage Scenarios</h3>
                        
                        <div class="info-box">
                            <strong>Free Balance:</strong> Use for daily rewards, login bonuses, temporary promotions<br>
                            <strong>Permanent Balance:</strong> Use for purchases, achievements, referral rewards, one-time bonuses
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
