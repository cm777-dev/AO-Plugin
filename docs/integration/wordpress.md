# WordPress Integration

This guide covers how to integrate Agent Optimization Pro with various WordPress features and components.

## Theme Integration

### Adding Theme Support

```php
// functions.php
add_theme_support('aop-features', [
    'structured-data',
    'content-optimization',
    'search-enhancement'
]);
```

### Template Integration

```php
// single.php
if (function_exists('aop_get_schema')) {
    $schema = aop_get_schema(get_the_ID());
    echo '<script type="application/ld+json">';
    echo wp_json_encode($schema);
    echo '</script>';
}
```

## Plugin Compatibility

### Action Integration

```php
// Hook into plugin actions
add_action('aop_after_optimization', function($post_id) {
    // Custom functionality after optimization
});
```

### Filter Integration

```php
// Modify optimization results
add_filter('aop_optimization_results', function($results, $post_id) {
    // Customize results
    return $results;
}, 10, 2);
```

## Custom Post Types

### Register Support

```php
// Register CPT support
add_post_type_support('product', 'aop-features');

// Custom schema mapping
add_filter('aop_schema_mapping', function($mapping) {
    $mapping['product'] = [
        'type' => 'Product',
        'properties' => [
            'price' => 'meta_price',
            'sku' => 'meta_sku'
        ]
    ];
    return $mapping;
});
```

## Gutenberg Integration

### Custom Blocks

```php
// Register custom block
register_block_type('aop/optimization-score', [
    'editor_script' => 'aop-blocks',
    'render_callback' => function($attributes) {
        $score = aop_get_optimization_score(get_the_ID());
        return sprintf(
            '<div class="aop-score">%d</div>',
            esc_html($score)
        );
    }
]);
```

### Block Filters

```php
// Add optimization data to blocks
add_filter('render_block', function($block_content, $block) {
    if ($block['blockName'] === 'core/paragraph') {
        $optimization = aop_analyze_content($block_content);
        return $block_content . aop_get_optimization_markup($optimization);
    }
    return $block_content;
}, 10, 2);
```

## Widget Integration

### Custom Widget

```php
class AOP_Optimization_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'aop_optimization_widget',
            'Optimization Score'
        );
    }

    public function widget($args, $instance) {
        $score = aop_get_optimization_score(get_the_ID());
        echo $args['before_widget'];
        echo '<div class="optimization-score">' . esc_html($score) . '</div>';
        echo $args['after_widget'];
    }
}

// Register widget
add_action('widgets_init', function() {
    register_widget('AOP_Optimization_Widget');
});
```

## Admin Integration

### Custom Meta Boxes

```php
// Add meta box
add_action('add_meta_boxes', function() {
    add_meta_box(
        'aop_optimization',
        'Content Optimization',
        'render_optimization_meta_box',
        'post'
    );
});

function render_optimization_meta_box($post) {
    $score = aop_get_optimization_score($post->ID);
    $suggestions = aop_get_optimization_suggestions($post->ID);
    
    include AOP_PLUGIN_DIR . 'templates/meta-box.php';
}
```

### Admin Pages

```php
// Add admin menu
add_action('admin_menu', function() {
    add_menu_page(
        'Optimization',
        'AOP Settings',
        'manage_options',
        'aop-settings',
        'render_settings_page'
    );
});
```

## REST API Integration

### Custom Endpoints

```php
// Register REST route
add_action('rest_api_init', function() {
    register_rest_route('aop/v1', '/optimize/(?P<id>\d+)', [
        'methods' => 'POST',
        'callback' => 'handle_optimization_request',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);
});
```

## WooCommerce Integration

### Product Integration

```php
// Add product support
add_action('woocommerce_product_options_general_product_data', function() {
    woocommerce_wp_text_input([
        'id' => '_aop_optimization_score',
        'label' => 'Optimization Score',
        'type' => 'number'
    ]);
});

// Save product data
add_action('woocommerce_process_product_meta', function($post_id) {
    $score = aop_get_optimization_score($post_id);
    update_post_meta($post_id, '_aop_optimization_score', $score);
});
```

## Multisite Integration

### Network Settings

```php
// Network activation
register_activation_hook(__FILE__, function() {
    if (is_multisite()) {
        foreach (get_sites() as $site) {
            switch_to_blog($site->blog_id);
            aop_activate_site();
            restore_current_blog();
        }
    }
});
```

## Performance Optimization

### Caching Integration

```php
// Cache integration
add_action('save_post', function($post_id) {
    wp_cache_delete('aop_optimization_' . $post_id);
});

// Get cached data
function aop_get_cached_optimization($post_id) {
    $cached = wp_cache_get('aop_optimization_' . $post_id);
    if (false === $cached) {
        $cached = aop_calculate_optimization($post_id);
        wp_cache_set('aop_optimization_' . $post_id, $cached);
    }
    return $cached;
}
```

## Security Considerations

### Capability Checks

```php
// Check permissions
function aop_can_optimize() {
    return current_user_can('edit_posts');
}

// Verify nonce
function aop_verify_request() {
    return check_ajax_referer('aop_nonce', false, false);
}
```

## Troubleshooting

Common integration issues and solutions:

1. **Plugin Conflicts**
   - Check for hook priority
   - Review filter modifications
   - Monitor error log
   - Test in isolation

2. **Performance Issues**
   - Enable caching
   - Optimize queries
   - Profile code
   - Monitor resources
