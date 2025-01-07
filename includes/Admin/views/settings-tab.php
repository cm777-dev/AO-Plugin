<?php
if (!defined('ABSPATH')) {
    exit;
}

// Save settings
if (isset($_POST['aop_save_settings']) && check_admin_referer('aop_settings')) {
    $settings = [
        'enable_structured_data' => isset($_POST['enable_structured_data']),
        'enable_api_generator' => isset($_POST['enable_api_generator']),
        'enable_content_optimizer' => isset($_POST['enable_content_optimizer']),
        'enable_agent_collaboration' => isset($_POST['enable_agent_collaboration']),
        'enable_search_integration' => isset($_POST['enable_search_integration']),
        'api_rate_limit' => intval($_POST['api_rate_limit']),
        'cache_duration' => intval($_POST['cache_duration']),
    ];
    
    update_option('aop_settings', $settings);
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully.', 'agent-optimization-pro') . '</p></div>';
}

// Get current settings
$settings = get_option('aop_settings', []);
?>

<form method="post" action="">
    <?php wp_nonce_field('aop_settings'); ?>
    
    <div class="aop-card">
        <h2><?php _e('General Settings', 'agent-optimization-pro'); ?></h2>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable Features', 'agent-optimization-pro'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="enable_structured_data" 
                                   <?php checked($settings['enable_structured_data'] ?? true); ?>>
                            <?php _e('Structured Data Generation', 'agent-optimization-pro'); ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="enable_api_generator" 
                                   <?php checked($settings['enable_api_generator'] ?? true); ?>>
                            <?php _e('API Generator', 'agent-optimization-pro'); ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="enable_content_optimizer" 
                                   <?php checked($settings['enable_content_optimizer'] ?? true); ?>>
                            <?php _e('Content Optimizer', 'agent-optimization-pro'); ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="enable_agent_collaboration" 
                                   <?php checked($settings['enable_agent_collaboration'] ?? true); ?>>
                            <?php _e('Agent Collaboration', 'agent-optimization-pro'); ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="enable_search_integration" 
                                   <?php checked($settings['enable_search_integration'] ?? true); ?>>
                            <?php _e('Search Integration', 'agent-optimization-pro'); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="api_rate_limit"><?php _e('API Rate Limit', 'agent-optimization-pro'); ?></label>
                </th>
                <td>
                    <input type="number" id="api_rate_limit" name="api_rate_limit" 
                           value="<?php echo esc_attr($settings['api_rate_limit'] ?? 60); ?>" min="1" max="1000">
                    <p class="description">
                        <?php _e('Maximum number of API requests per minute per IP address.', 'agent-optimization-pro'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cache_duration"><?php _e('Cache Duration', 'agent-optimization-pro'); ?></label>
                </th>
                <td>
                    <input type="number" id="cache_duration" name="cache_duration" 
                           value="<?php echo esc_attr($settings['cache_duration'] ?? 3600); ?>" min="0">
                    <p class="description">
                        <?php _e('Duration in seconds to cache API responses. Set to 0 to disable caching.', 'agent-optimization-pro'); ?>
                    </p>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="aop-card">
        <h2><?php _e('Structured Data Settings', 'agent-optimization-pro'); ?></h2>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Schema Types', 'agent-optimization-pro'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="schema_article" 
                                   <?php checked($settings['schema_article'] ?? true); ?>>
                            <?php _e('Article Schema', 'agent-optimization-pro'); ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="schema_organization" 
                                   <?php checked($settings['schema_organization'] ?? true); ?>>
                            <?php _e('Organization Schema', 'agent-optimization-pro'); ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="schema_breadcrumb" 
                                   <?php checked($settings['schema_breadcrumb'] ?? true); ?>>
                            <?php _e('Breadcrumb Schema', 'agent-optimization-pro'); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="aop-card">
        <h2><?php _e('Content Optimization Settings', 'agent-optimization-pro'); ?></h2>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="min_content_length">
                        <?php _e('Minimum Content Length', 'agent-optimization-pro'); ?>
                    </label>
                </th>
                <td>
                    <input type="number" id="min_content_length" name="min_content_length" 
                           value="<?php echo esc_attr($settings['min_content_length'] ?? 300); ?>" min="0">
                    <p class="description">
                        <?php _e('Minimum number of words recommended for optimal content.', 'agent-optimization-pro'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Analysis Features', 'agent-optimization-pro'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="enable_readability_check" 
                                   <?php checked($settings['enable_readability_check'] ?? true); ?>>
                            <?php _e('Readability Check', 'agent-optimization-pro'); ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="enable_keyword_suggestions" 
                                   <?php checked($settings['enable_keyword_suggestions'] ?? true); ?>>
                            <?php _e('Keyword Suggestions', 'agent-optimization-pro'); ?>
                        </label><br>
                        
                        <label>
                            <input type="checkbox" name="enable_semantic_analysis" 
                                   <?php checked($settings['enable_semantic_analysis'] ?? true); ?>>
                            <?php _e('Semantic Analysis', 'agent-optimization-pro'); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>
    </div>
    
    <p class="submit">
        <input type="submit" name="aop_save_settings" class="button button-primary" 
               value="<?php esc_attr_e('Save Settings', 'agent-optimization-pro'); ?>">
    </p>
</form>
