<?php
if (!defined('ABSPATH')) {
    exit;
}

class SlimWP_Shortcodes {
    
    private $points_system;
    
    public function __construct($points_system) {
        $this->points_system = $points_system;
        
        // Register shortcodes
        add_shortcode('slimwp_points', array($this, 'points_shortcode'));
        add_shortcode('slimwp_points_free', array($this, 'points_free_shortcode'));
        add_shortcode('slimwp_points_permanent', array($this, 'points_permanent_shortcode'));
        
        // Backward compatibility shortcodes
        add_shortcode('user_points', array($this, 'points_shortcode'));
        add_shortcode('user_points_free', array($this, 'points_free_shortcode'));
        add_shortcode('user_points_permanent', array($this, 'points_permanent_shortcode'));
    }
    
    public function points_shortcode($atts) {
        $atts = shortcode_atts(array(
            'user_id' => get_current_user_id(),
            'type' => 'total' // 'total', 'free', 'permanent'
        ), $atts);
        
        if (!$atts['user_id']) {
            return '';
        }
        
        switch ($atts['type']) {
            case 'free':
                $balance = $this->points_system->get_free_balance($atts['user_id']);
                break;
            case 'permanent':
                $balance = $this->points_system->get_permanent_balance($atts['user_id']);
                break;
            default:
                $balance = $this->points_system->get_balance($atts['user_id']);
        }
        
        return '<span class="slimwp-points-balance">' . number_format($balance, 2) . '</span>';
    }
    
    public function points_free_shortcode($atts) {
        $atts = shortcode_atts(array('user_id' => get_current_user_id()), $atts);
        if (!$atts['user_id']) return '';
        return '<span class="slimwp-points-balance-free">' . number_format($this->points_system->get_free_balance($atts['user_id']), 2) . '</span>';
    }
    
    public function points_permanent_shortcode($atts) {
        $atts = shortcode_atts(array('user_id' => get_current_user_id()), $atts);
        if (!$atts['user_id']) return '';
        return '<span class="slimwp-points-balance-permanent">' . number_format($this->points_system->get_permanent_balance($atts['user_id']), 2) . '</span>';
    }
}
