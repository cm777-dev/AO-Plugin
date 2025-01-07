<?php
namespace AgentOptimizationPro\Core;

class Deactivator {
    public static function deactivate() {
        // Clear scheduled hooks
        wp_clear_scheduled_hooks('aop_daily_optimization');
        wp_clear_scheduled_hooks('aop_cleanup_old_data');
        
        // Clear rewrite rules
        flush_rewrite_rules();
        
        // Optionally clean up transients
        self::cleanup_transients();
    }

    private static function cleanup_transients() {
        global $wpdb;
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_aop_%' 
            OR option_name LIKE '_transient_timeout_aop_%'"
        );
    }
}
