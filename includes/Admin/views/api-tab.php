<?php
if (!defined('ABSPATH')) {
    exit;
}

// Handle API endpoint creation/update
if (isset($_POST['aop_save_endpoint']) && check_admin_referer('aop_api_endpoint')) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'aop_api_endpoints';
    
    $endpoint_data = [
        'endpoint_name' => sanitize_text_field($_POST['endpoint_name']),
        'endpoint_path' => sanitize_text_field($_POST['endpoint_path']),
        'endpoint_method' => sanitize_text_field($_POST['endpoint_method']),
        'endpoint_config' => json_encode([
            'post_type' => sanitize_text_field($_POST['post_type']),
            'posts_per_page' => intval($_POST['posts_per_page']),
            'orderby' => sanitize_text_field($_POST['orderby']),
            'order' => sanitize_text_field($_POST['order']),
        ]),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    if (isset($_POST['endpoint_id'])) {
        $wpdb->update(
            $table_name,
            $endpoint_data,
            ['id' => intval($_POST['endpoint_id'])]
        );
        echo '<div class="notice notice-success"><p>' . __('API endpoint updated successfully.', 'agent-optimization-pro') . '</p></div>';
    } else {
        $wpdb->insert($table_name, $endpoint_data);
        echo '<div class="notice notice-success"><p>' . __('API endpoint created successfully.', 'agent-optimization-pro') . '</p></div>';
    }
}

// Get existing endpoints
global $wpdb;
$endpoints = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aop_api_endpoints ORDER BY endpoint_name");
?>

<div class="aop-api-management">
    <div class="aop-card">
        <h2><?php _e('API Documentation', 'agent-optimization-pro'); ?></h2>
        <p>
            <?php _e('Base URL:', 'agent-optimization-pro'); ?>
            <code><?php echo esc_html(rest_url('agent-optimization-pro/v1')); ?></code>
        </p>
        <p>
            <?php _e('Authentication: Include your API key in the request headers as:', 'agent-optimization-pro'); ?>
            <code>X-API-Key: your-api-key</code>
        </p>
    </div>

    <div class="aop-card">
        <h2><?php _e('API Endpoints', 'agent-optimization-pro'); ?></h2>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Name', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Path', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Method', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Status', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Actions', 'agent-optimization-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($endpoints as $endpoint) : ?>
                    <tr>
                        <td><?php echo esc_html($endpoint->endpoint_name); ?></td>
                        <td><code><?php echo esc_html($endpoint->endpoint_path); ?></code></td>
                        <td><?php echo esc_html($endpoint->endpoint_method); ?></td>
                        <td>
                            <span class="aop-status-badge <?php echo $endpoint->is_active ? 'active' : 'inactive'; ?>">
                                <?php echo $endpoint->is_active ? __('Active', 'agent-optimization-pro') : __('Inactive', 'agent-optimization-pro'); ?>
                            </span>
                        </td>
                        <td>
                            <button class="button button-small aop-edit-endpoint" 
                                    data-endpoint='<?php echo esc_attr(json_encode($endpoint)); ?>'>
                                <?php _e('Edit', 'agent-optimization-pro'); ?>
                            </button>
                            <button class="button button-small aop-test-endpoint" 
                                    data-path="<?php echo esc_attr($endpoint->endpoint_path); ?>">
                                <?php _e('Test', 'agent-optimization-pro'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p>
            <button class="button button-primary" id="aop-add-endpoint">
                <?php _e('Add New Endpoint', 'agent-optimization-pro'); ?>
            </button>
        </p>
    </div>
</div>

<!-- Endpoint Modal -->
<div id="aop-endpoint-modal" class="aop-modal">
    <div class="aop-modal-content">
        <span class="aop-modal-close">&times;</span>
        <h2 id="aop-modal-title"><?php _e('Add API Endpoint', 'agent-optimization-pro'); ?></h2>
        
        <form method="post" action="">
            <?php wp_nonce_field('aop_api_endpoint'); ?>
            <input type="hidden" name="endpoint_id" id="endpoint_id">
            
            <p>
                <label for="endpoint_name"><?php _e('Endpoint Name', 'agent-optimization-pro'); ?></label>
                <input type="text" id="endpoint_name" name="endpoint_name" required>
            </p>
            
            <p>
                <label for="endpoint_path"><?php _e('Endpoint Path', 'agent-optimization-pro'); ?></label>
                <input type="text" id="endpoint_path" name="endpoint_path" required>
                <span class="description"><?php _e('Example: /posts/featured', 'agent-optimization-pro'); ?></span>
            </p>
            
            <p>
                <label for="endpoint_method"><?php _e('HTTP Method', 'agent-optimization-pro'); ?></label>
                <select id="endpoint_method" name="endpoint_method">
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="DELETE">DELETE</option>
                </select>
            </p>
            
            <p>
                <label for="post_type"><?php _e('Post Type', 'agent-optimization-pro'); ?></label>
                <select id="post_type" name="post_type">
                    <?php
                    $post_types = get_post_types(['public' => true], 'objects');
                    foreach ($post_types as $post_type) {
                        echo '<option value="' . esc_attr($post_type->name) . '">' . 
                             esc_html($post_type->labels->singular_name) . '</option>';
                    }
                    ?>
                </select>
            </p>
            
            <p>
                <label for="posts_per_page"><?php _e('Posts Per Page', 'agent-optimization-pro'); ?></label>
                <input type="number" id="posts_per_page" name="posts_per_page" value="10" min="1" max="100">
            </p>
            
            <p>
                <label for="orderby"><?php _e('Order By', 'agent-optimization-pro'); ?></label>
                <select id="orderby" name="orderby">
                    <option value="date">Date</option>
                    <option value="title">Title</option>
                    <option value="menu_order">Menu Order</option>
                    <option value="rand">Random</option>
                </select>
            </p>
            
            <p>
                <label for="order"><?php _e('Order', 'agent-optimization-pro'); ?></label>
                <select id="order" name="order">
                    <option value="DESC">Descending</option>
                    <option value="ASC">Ascending</option>
                </select>
            </p>
            
            <p>
                <label>
                    <input type="checkbox" name="is_active" id="is_active" checked>
                    <?php _e('Active', 'agent-optimization-pro'); ?>
                </label>
            </p>
            
            <p class="submit">
                <input type="submit" name="aop_save_endpoint" class="button button-primary" 
                       value="<?php esc_attr_e('Save Endpoint', 'agent-optimization-pro'); ?>">
            </p>
        </form>
    </div>
</div>

<style>
.aop-status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.aop-status-badge.active {
    background: #46b450;
    color: white;
}

.aop-status-badge.inactive {
    background: #dc3232;
    color: white;
}

.aop-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.aop-modal-content {
    position: relative;
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    width: 70%;
    max-width: 600px;
    border-radius: 4px;
}

.aop-modal-close {
    position: absolute;
    right: 10px;
    top: 10px;
    font-size: 24px;
    cursor: pointer;
}

.aop-modal form label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.aop-modal form input[type="text"],
.aop-modal form select {
    width: 100%;
    margin-bottom: 15px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Modal functionality
    const modal = $('#aop-endpoint-modal');
    const modalTitle = $('#aop-modal-title');
    const form = modal.find('form');
    
    $('#aop-add-endpoint').click(function() {
        modalTitle.text('<?php _e('Add API Endpoint', 'agent-optimization-pro'); ?>');
        form[0].reset();
        $('#endpoint_id').val('');
        modal.show();
    });
    
    $('.aop-edit-endpoint').click(function() {
        const endpoint = $(this).data('endpoint');
        modalTitle.text('<?php _e('Edit API Endpoint', 'agent-optimization-pro'); ?>');
        
        $('#endpoint_id').val(endpoint.id);
        $('#endpoint_name').val(endpoint.endpoint_name);
        $('#endpoint_path').val(endpoint.endpoint_path);
        $('#endpoint_method').val(endpoint.endpoint_method);
        
        const config = JSON.parse(endpoint.endpoint_config);
        $('#post_type').val(config.post_type);
        $('#posts_per_page').val(config.posts_per_page);
        $('#orderby').val(config.orderby);
        $('#order').val(config.order);
        $('#is_active').prop('checked', endpoint.is_active == 1);
        
        modal.show();
    });
    
    $('.aop-modal-close').click(function() {
        modal.hide();
    });
    
    $(window).click(function(e) {
        if ($(e.target).is(modal)) {
            modal.hide();
        }
    });
    
    // Test endpoint functionality
    $('.aop-test-endpoint').click(function() {
        const path = $(this).data('path');
        const button = $(this);
        
        button.prop('disabled', true).text('<?php _e('Testing...', 'agent-optimization-pro'); ?>');
        
        wp.apiRequest({
            path: 'agent-optimization-pro/v1' + path,
            method: 'GET'
        }).done(function(response) {
            alert('<?php _e('Test successful! Check browser console for response.', 'agent-optimization-pro'); ?>');
            console.log('API Response:', response);
        }).fail(function(error) {
            alert('<?php _e('Test failed! Check browser console for error details.', 'agent-optimization-pro'); ?>');
            console.error('API Error:', error);
        }).always(function() {
            button.prop('disabled', false).text('<?php _e('Test', 'agent-optimization-pro'); ?>');
        });
    });
});
</script>
