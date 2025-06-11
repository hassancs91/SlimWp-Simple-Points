<?php
// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove options
delete_option('slimwp_points_hooks');

// Remove tables
global $wpdb;
$table_name = $wpdb->prefix . 'slimwp_user_points_transactions';
$wpdb->query("DROP TABLE IF EXISTS {$table_name}");

// Remove user meta
$wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key IN ('slimwp_points_balance', 'slimwp_points_balance_permanent', 'slimwp_last_login_bonus_date', 'slimwp_last_daily_reset_date', 'slimwp_last_monthly_reset_date')");
