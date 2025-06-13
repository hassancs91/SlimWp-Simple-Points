<?php
if (!defined('ABSPATH')) {
    exit;
}

class SlimWP_Ajax {
    
    private $points_system;
    
    public function __construct($points_system) {
        $this->points_system = $points_system;
        
        // AJAX handlers
        add_action('wp_ajax_slimwp_update_user_points', array($this, 'ajax_update_user_points'));
    }
    
    /**
     * Rate limiting for AJAX requests
     */
    private function check_rate_limit($action, $limit = 10, $window = 300) {
        $user_id = get_current_user_id();
        $ip_address = $this->get_client_ip();
        $rate_key = 'slimwp_rate_' . $action . '_' . $user_id . '_' . md5($ip_address);
        
        $current_requests = get_transient($rate_key) ?: 0;
        
        if ($current_requests >= $limit) {
            error_log('SlimWP Security: Rate limit exceeded for user ' . $user_id . ' from IP ' . $ip_address . ' on action ' . $action);
            wp_die('Rate limit exceeded. Please wait before trying again.', 'Rate Limit', array('response' => 429));
        }
        
        set_transient($rate_key, $current_requests + 1, $window);
    }
    
    /**
     * Get client IP address securely
     */
    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        );
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = sanitize_text_field($_SERVER[$key]);
                
                // Handle comma-separated IPs (X-Forwarded-For can contain multiple IPs)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                
                // Validate IP address
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        // Fallback to REMOTE_ADDR even if it's private/reserved
        return sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    }
    
    public function ajax_update_user_points() {
        // Rate limiting check
        $this->check_rate_limit('update_user_points', 20, 300); // 20 requests per 5 minutes
        
        // Verify nonce
        if (!check_ajax_referer('slimwp_nonce', 'nonce', false)) {
            error_log('SlimWP Security: Invalid nonce in ajax_update_user_points from user ' . get_current_user_id());
            wp_send_json_error('Security check failed.');
        }
        
        // Check capabilities
        if (!current_user_can('edit_users')) {
            error_log('SlimWP Security: Unauthorized ajax_update_user_points attempt from user ' . get_current_user_id());
            wp_send_json_error('Unauthorized access.');
        }
        
        // Validate and sanitize input
        $user_id = intval($_POST['user_id']);
        $amount = floatval($_POST['amount']);
        $description = sanitize_text_field($_POST['description']);
        $operation = sanitize_text_field($_POST['operation']);
        $balance_type = isset($_POST['balance_type']) ? sanitize_text_field($_POST['balance_type']) : 'free';
        
        // Additional input validation
        if ($user_id <= 0 || !user_can($user_id, 'read')) {
            wp_send_json_error('Invalid user ID.');
        }
        
        if ($amount < 0 || $amount > 999999999) {
            wp_send_json_error('Invalid amount. Must be between 0 and 999,999,999.');
        }
        
        if (empty($description) || strlen($description) > 255) {
            wp_send_json_error('Description is required and must be less than 255 characters.');
        }
        
        $allowed_operations = array('add', 'subtract');
        if (!in_array($operation, $allowed_operations)) {
            error_log('SlimWP Security: Invalid operation attempted in AJAX: ' . $operation);
            wp_send_json_error('Invalid operation.');
        }
        
        $allowed_balance_types = array('free', 'permanent');
        if (!in_array($balance_type, $allowed_balance_types)) {
            error_log('SlimWP Security: Invalid balance type attempted in AJAX: ' . $balance_type);
            wp_send_json_error('Invalid balance type.');
        }
        
        // Log the operation attempt
        error_log('SlimWP AJAX: Points update - User: ' . $user_id . ', Operation: ' . $operation . ', Amount: ' . $amount . ', Balance Type: ' . $balance_type);
        
        // Perform the operation
        if ($operation === 'add') {
            $result = $this->points_system->add_points($user_id, $amount, $description, 'manual', $balance_type);
        } else {
            $result = $this->points_system->subtract_points($user_id, $amount, $description);
        }
        
        if (is_wp_error($result)) {
            error_log('SlimWP AJAX Error: ' . $result->get_error_message());
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success(array(
                'new_balance' => $result,
                'free_balance' => $this->points_system->get_free_balance($user_id),
                'permanent_balance' => $this->points_system->get_permanent_balance($user_id)
            ));
        }
    }
}
