<?php
namespace AgentOptimizationPro\Core;

class Activator {
    public static function activate() {
        // Create necessary database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Clear rewrite rules
        flush_rewrite_rules();
    }

    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Create optimization history table
        $table_name = $wpdb->prefix . 'aop_optimization_history';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            optimization_type varchar(50) NOT NULL,
            optimization_data longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id)
        ) $charset_collate;";

        // Create API endpoints table
        $api_table = $wpdb->prefix . 'aop_api_endpoints';
        $sql .= "CREATE TABLE IF NOT EXISTS $api_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            endpoint_name varchar(100) NOT NULL,
            endpoint_path varchar(255) NOT NULL,
            endpoint_method varchar(10) NOT NULL,
            endpoint_config longtext NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY endpoint_path (endpoint_path)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private static function set_default_options() {
        // General settings
        add_option('aop_settings', [
            'enable_structured_data' => true,
            'enable_api_generator' => true,
            'enable_content_optimizer' => true,
            'enable_agent_collaboration' => true,
            'enable_search_integration' => true,
            'api_rate_limit' => 60,
            'cache_duration' => 3600
        ]);

        // Structured data settings
        add_option('aop_structured_data_settings', [
            'enable_article_schema' => true,
            'enable_organization_schema' => true,
            'enable_breadcrumb_schema' => true,
            'enable_opengraph' => true,
            'enable_twitter_cards' => true
        ]);

        // Content optimization settings
        add_option('aop_content_optimization_settings', [
            'min_content_length' => 300,
            'enable_readability_check' => true,
            'enable_keyword_suggestions' => true,
            'enable_semantic_analysis' => true
        ]);
    }
}
