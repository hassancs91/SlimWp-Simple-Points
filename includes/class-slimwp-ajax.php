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
    
    public function ajax_update_user_points() {
        check_ajax_referer('slimwp_nonce', 'nonce');
        
        if (!current_user_can('edit_users')) {
            wp_die('Unauthorized');
        }
        
        $user_id = intval($_POST['user_id']);
        $amount = floatval($_POST['amount']);
        $description = sanitize_text_field($_POST['description']);
        $operation = sanitize_text_field($_POST['operation']);
        $balance_type = isset($_POST['balance_type']) ? sanitize_text_field($_POST['balance_type']) : 'free';
        
        if ($operation === 'add') {
            $result = $this->points_system->add_points($user_id, $amount, $description, 'manual', $balance_type);
        } else {
            $result = $this->points_system->subtract_points($user_id, $amount, $description);
        }
        
        if (is_wp_error($result)) {
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
