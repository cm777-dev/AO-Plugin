<?php
/**
 * Plugin Name: Agent Optimization Pro
 * Plugin URI: https://example.com/agent-optimization-pro
 * Description: The ultimate WordPress plugin to prepare your website for the future of AI-driven discovery and decision-making.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: agent-optimization-pro
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('AOP_VERSION', '1.0.0');
define('AOP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AOP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'AgentOptimizationPro\\';
    $base_dir = AOP_PLUGIN_DIR . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
function aop_init() {
    // Load text domain for internationalization
    load_plugin_textdomain('agent-optimization-pro', false, dirname(plugin_basename(__FILE__)) . '/languages');
    
    // Initialize main plugin class
    \AgentOptimizationPro\Core\Plugin::get_instance();
}
add_action('plugins_loaded', 'aop_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    // Create necessary database tables and set default options
    require_once AOP_PLUGIN_DIR . 'includes/Core/Activator.php';
    \AgentOptimizationPro\Core\Activator::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Clean up if necessary
    require_once AOP_PLUGIN_DIR . 'includes/Core/Deactivator.php';
    \AgentOptimizationPro\Core\Deactivator::deactivate();
});
