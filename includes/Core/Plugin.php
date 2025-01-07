<?php
namespace AgentOptimizationPro\Core;

class Plugin {
    private static $instance = null;
    private $modules = [];

    private function __construct() {
        $this->init_modules();
        $this->setup_hooks();
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function init_modules() {
        // Initialize all plugin modules
        $this->modules = [
            'structured_data' => new \AgentOptimizationPro\Modules\StructuredData(),
            'api_generator' => new \AgentOptimizationPro\Modules\ApiGenerator(),
            'content_optimizer' => new \AgentOptimizationPro\Modules\ContentOptimizer(),
            'agent_collaboration' => new \AgentOptimizationPro\Modules\AgentCollaboration(),
            'search_integration' => new \AgentOptimizationPro\Modules\SearchIntegration(),
        ];
    }

    private function setup_hooks() {
        // Admin menu
        add_action('admin_menu', [$this, 'add_admin_menu']);
        // Admin assets
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        // REST API initialization
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Agent Optimization Pro', 'agent-optimization-pro'),
            __('Agent Optimization', 'agent-optimization-pro'),
            'manage_options',
            'agent-optimization-pro',
            [$this, 'render_admin_page'],
            'dashicons-superhero',
            30
        );
    }

    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_agent-optimization-pro' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'agent-optimization-pro-admin',
            AOP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            AOP_VERSION
        );

        wp_enqueue_script(
            'agent-optimization-pro-admin',
            AOP_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'wp-api'],
            AOP_VERSION,
            true
        );
    }

    public function register_rest_routes() {
        // Register REST API routes for each module
        foreach ($this->modules as $module) {
            if (method_exists($module, 'register_routes')) {
                $module->register_routes();
            }
        }
    }

    public function render_admin_page() {
        require_once AOP_PLUGIN_DIR . 'includes/Admin/views/main-page.php';
    }
}
