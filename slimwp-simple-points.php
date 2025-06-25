<?php
/**
 * Plugin Name: SlimWP Simple Points
 * Plugin URI: https://yourwebsite.com/slimwp-simple-points
 * Description: A lightweight dual-balance points system for WordPress with free and permanent points tracking.
 * Version: 1.0.4
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: SlimWp-Simple-Points
 * Domain Path: /languages
 */


// Include the update checker
// Include the update checker
require_once 'plugin-update-checker-master/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

// Setup the update checker
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/hassancs91/SlimWp-Simple-Points/',
    __FILE__,
    'slimwp-simple-points'
);

// Set authentication token for private repo
$myUpdateChecker->setAuthentication('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');

// Set the branch to main (or master, depending on your repository)
$myUpdateChecker->setBranch('main');




// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SLIMWP_VERSION', '1.0.0');
define('SLIMWP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SLIMWP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SLIMWP_PLUGIN_FILE', __FILE__);

// Include required files
require_once SLIMWP_PLUGIN_DIR . 'includes/class-slimwp-database.php';
require_once SLIMWP_PLUGIN_DIR . 'includes/class-slimwp-hooks.php';
require_once SLIMWP_PLUGIN_DIR . 'includes/class-slimwp-shortcodes.php';
require_once SLIMWP_PLUGIN_DIR . 'includes/class-slimwp-ajax.php';
require_once SLIMWP_PLUGIN_DIR . 'includes/class-slimwp-user-profile.php';
require_once SLIMWP_PLUGIN_DIR . 'includes/class-slimwp-admin.php';
require_once SLIMWP_PLUGIN_DIR . 'includes/class-slimwp-settings.php';
require_once SLIMWP_PLUGIN_DIR . 'includes/class-slimwp-points.php';
require_once SLIMWP_PLUGIN_DIR . 'includes/class-slimwp-woocommerce.php';
require_once SLIMWP_PLUGIN_DIR . 'includes/class-slimwp-stripe-database.php';
require_once SLIMWP_PLUGIN_DIR . 'includes/class-slimwp-stripe.php';
require_once SLIMWP_PLUGIN_DIR . 'includes/class-slimwp-stripe-packages.php';
require_once SLIMWP_PLUGIN_DIR . 'includes/functions.php';

// Initialize the plugin
function slimwp_init() {
    return SlimWP_Points::get_instance();
}
add_action('plugins_loaded', 'slimwp_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    SlimWP_Database::create_tables();
    SlimWP_Stripe_Database::create_tables();
});
