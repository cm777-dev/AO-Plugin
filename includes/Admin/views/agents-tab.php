<?php
if (!defined('ABSPATH')) {
    exit;
}

// Handle agent key generation
if (isset($_POST['aop_generate_key']) && check_admin_referer('aop_agent_key')) {
    $new_key = wp_generate_password(32, false);
    $api_keys = get_option('aop_api_keys', []);
    $api_keys[] = $new_key;
    update_option('aop_api_keys', $api_keys);
    echo '<div class="notice notice-success"><p>' . 
         __('New API key generated successfully:', 'agent-optimization-pro') . 
         ' <code>' . esc_html($new_key) . '</code></p></div>';
}

// Get registered agents
$agents = get_posts([
    'post_type' => 'aop_agent',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
]);
?>

<div class="aop-agents">
    <div class="aop-card">
        <h2><?php _e('API Keys', 'agent-optimization-pro'); ?></h2>
        
        <form method="post" action="">
            <?php wp_nonce_field('aop_agent_key'); ?>
            <p>
                <input type="submit" name="aop_generate_key" class="button button-primary" 
                       value="<?php esc_attr_e('Generate New API Key', 'agent-optimization-pro'); ?>">
            </p>
        </form>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('API Key', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Created', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Actions', 'agent-optimization-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $api_keys = get_option('aop_api_keys', []);
                foreach ($api_keys as $key) :
                ?>
                <tr>
                    <td><code><?php echo esc_html($key); ?></code></td>
                    <td><?php echo esc_html(get_option('aop_key_created_' . $key, '')); ?></td>
                    <td>
                        <button class="button button-small aop-revoke-key" data-key="<?php echo esc_attr($key); ?>">
                            <?php _e('Revoke', 'agent-optimization-pro'); ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="aop-card">
        <h2><?php _e('Registered AI Agents', 'agent-optimization-pro'); ?></h2>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Agent Name', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Capabilities', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Status', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Last Active', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Actions', 'agent-optimization-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agents as $agent) :
                    $capabilities = get_post_meta($agent->ID, '_aop_agent_capabilities', true);
                    $status = get_post_meta($agent->ID, '_aop_agent_status', true);
                    $last_active = get_post_meta($agent->ID, '_aop_agent_last_active', true);
                ?>
                <tr>
                    <td><?php echo esc_html($agent->post_title); ?></td>
                    <td>
                        <?php
                        if (is_array($capabilities)) {
                            echo esc_html(implode(', ', $capabilities));
                        }
                        ?>
                    </td>
                    <td>
                        <span class="aop-status-badge <?php echo esc_attr($status); ?>">
                            <?php echo esc_html(ucfirst($status)); ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        if ($last_active) {
                            echo esc_html(human_time_diff($last_active, time()) . ' ago');
                        } else {
                            _e('Never', 'agent-optimization-pro');
                        }
                        ?>
                    </td>
                    <td>
                        <button class="button button-small aop-view-agent" data-agent-id="<?php echo esc_attr($agent->ID); ?>">
                            <?php _e('View', 'agent-optimization-pro'); ?>
                        </button>
                        <button class="button button-small aop-deactivate-agent" 
                                data-agent-id="<?php echo esc_attr($agent->ID); ?>"
                                <?php echo $status === 'inactive' ? 'disabled' : ''; ?>>
                            <?php _e('Deactivate', 'agent-optimization-pro'); ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Agent Details Modal -->
<div id="aop-agent-modal" class="aop-modal">
    <div class="aop-modal-content">
        <span class="aop-modal-close">&times;</span>
        <h2><?php _e('Agent Details', 'agent-optimization-pro'); ?></h2>
        <div id="aop-agent-details"></div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Revoke API key
    $('.aop-revoke-key').click(function() {
        if (!confirm('<?php _e('Are you sure you want to revoke this API key?', 'agent-optimization-pro'); ?>')) {
            return;
        }
        
        const key = $(this).data('key');
        wp.apiRequest({
            path: 'agent-optimization-pro/v1/api-keys/' + key,
            method: 'DELETE'
        }).done(function() {
            location.reload();
        });
    });
    
    // View agent details
    $('.aop-view-agent').click(function() {
        const agentId = $(this).data('agent-id');
        const modal = $('#aop-agent-modal');
        const detailsContainer = $('#aop-agent-details');
        
        detailsContainer.html('<?php _e('Loading...', 'agent-optimization-pro'); ?>');
        modal.show();
        
        wp.apiRequest({
            path: 'agent-optimization-pro/v1/agents/' + agentId,
            method: 'GET'
        }).done(function(response) {
            let html = '<table class="widefat">';
            html += '<tr><th><?php _e('Agent ID', 'agent-optimization-pro'); ?></th><td>' + response.id + '</td></tr>';
            html += '<tr><th><?php _e('Name', 'agent-optimization-pro'); ?></th><td>' + response.name + '</td></tr>';
            html += '<tr><th><?php _e('Capabilities', 'agent-optimization-pro'); ?></th><td>' + response.capabilities.join(', ') + '</td></tr>';
            html += '<tr><th><?php _e('Callback URL', 'agent-optimization-pro'); ?></th><td>' + response.callback_url + '</td></tr>';
            html += '<tr><th><?php _e('Status', 'agent-optimization-pro'); ?></th><td>' + response.status + '</td></tr>';
            html += '<tr><th><?php _e('Last Active', 'agent-optimization-pro'); ?></th><td>' + response.last_active + '</td></tr>';
            html += '</table>';
            
            detailsContainer.html(html);
        });
    });
    
    // Deactivate agent
    $('.aop-deactivate-agent').click(function() {
        if (!confirm('<?php _e('Are you sure you want to deactivate this agent?', 'agent-optimization-pro'); ?>')) {
            return;
        }
        
        const agentId = $(this).data('agent-id');
        wp.apiRequest({
            path: 'agent-optimization-pro/v1/agents/' + agentId + '/deactivate',
            method: 'POST'
        }).done(function() {
            location.reload();
        });
    });
    
    // Modal close functionality
    $('.aop-modal-close, .aop-modal').click(function(e) {
        if (e.target === this) {
            $('.aop-modal').hide();
        }
    });
});
</script>
