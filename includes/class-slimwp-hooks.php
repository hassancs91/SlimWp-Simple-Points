<?php
if (!defined('ABSPATH')) {
    exit;
}

class SlimWP_Hooks {
    
    private $points_system;
    private $hooks_option;
    
    public function __construct($points_system) {
        $this->points_system = $points_system;
        $this->hooks_option = $points_system->get_hooks_option();
        
        // Core hooks
        add_action('user_register', array($this, 'points_on_register'));
        add_action('wp_login', array($this, 'points_on_login'), 10, 2);
    }
    
    public function points_on_register($user_id) {
        $hooks = get_option($this->hooks_option, array());
        if (!empty($hooks['register'])) {
            $points = isset($hooks['register_points']) ? intval($hooks['register_points']) : 100;
            $balance_type = isset($hooks['register_balance_type']) ? $hooks['register_balance_type'] : 'permanent';
            $this->points_system->add_points($user_id, $points, 'Welcome bonus', 'registration', $balance_type);
            do_action('slimwp_points_after_registration', $user_id);
        }
    }
    
    public function points_on_login($user_login, $user) {
        $hooks = get_option($this->hooks_option, array());
        
        // Daily Login Bonus (adds to balance)
        if (!empty($hooks['daily_login'])) {
            // Check if user already got points today
            $last_login_bonus = get_user_meta($user->ID, 'slimwp_last_login_bonus_date', true);
            $today = date('Y-m-d');
            
            if ($last_login_bonus !== $today) {
                $points = isset($hooks['daily_login_points']) ? intval($hooks['daily_login_points']) : 10;
                $balance_type = isset($hooks['daily_login_balance_type']) ? $hooks['daily_login_balance_type'] : 'free';
                $this->points_system->add_points($user->ID, $points, 'Daily login bonus', 'daily_login', $balance_type);
                update_user_meta($user->ID, 'slimwp_last_login_bonus_date', $today);
            }
        }
        
        // Daily Balance Reset (sets FREE balance to fixed amount)
        if (!empty($hooks['daily_reset'])) {
            $last_daily_reset = get_user_meta($user->ID, 'slimwp_last_daily_reset_date', true);
            $today = date('Y-m-d');
            
            if ($last_daily_reset !== $today) {
                $reset_amount = isset($hooks['daily_reset_points']) ? intval($hooks['daily_reset_points']) : 100;
                $this->points_system->set_balance($user->ID, $reset_amount, 'Daily balance reset', 'daily_reset', 'free');
                update_user_meta($user->ID, 'slimwp_last_daily_reset_date', $today);
            }
        }
        
        // Monthly Balance Reset (sets FREE balance to fixed amount)
        if (!empty($hooks['monthly_reset'])) {
            $last_monthly_reset = get_user_meta($user->ID, 'slimwp_last_monthly_reset_date', true);
            $current_month = date('Y-m');
            
            if ($last_monthly_reset !== $current_month) {
                $reset_amount = isset($hooks['monthly_reset_points']) ? intval($hooks['monthly_reset_points']) : 1000;
                $this->points_system->set_balance($user->ID, $reset_amount, 'Monthly balance reset', 'monthly_reset', 'free');
                update_user_meta($user->ID, 'slimwp_last_monthly_reset_date', $current_month);
            }
        }
    }
}

// Example hook for developers to extend functionality
add_action('slimwp_points_after_registration', function($user_id) {
    // Example: Add extra permanent points based on referral
    if (isset($_COOKIE['referral_code'])) {
        slimwp_add_user_points($user_id, 50, 'Referral bonus', 'permanent');
    }
}, 10, 1);
