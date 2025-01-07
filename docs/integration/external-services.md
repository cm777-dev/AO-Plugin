# External Services Integration

This guide covers integrating Agent Optimization Pro with external services and platforms.

## Supported Services

### AI Services
- OpenAI
- Google AI
- Azure Cognitive Services
- Custom AI endpoints

### Analytics Platforms
- Google Analytics
- Adobe Analytics
- Custom analytics services

### Search Engines
- Google Search Console
- Bing Webmaster Tools
- Custom search providers

### Content Delivery
- Cloudflare
- Akamai
- Custom CDN services

## AI Service Integration

### OpenAI Integration

```php
// Configure OpenAI
add_filter('aop_ai_service_config', function($config) {
    return [
        'provider' => 'openai',
        'api_key' => 'your-api-key',
        'model' => 'gpt-4',
        'temperature' => 0.7
    ];
});

// Custom prompt handling
add_filter('aop_ai_prompt', function($prompt, $context) {
    return sprintf(
        "Analyze the following content: %s\nContext: %s",
        $prompt,
        $context
    );
}, 10, 2);
```

### Custom AI Integration

```php
// Register custom AI service
aop_register_ai_service([
    'name' => 'custom_ai',
    'endpoint' => 'https://api.custom-ai.com',
    'auth' => [
        'type' => 'bearer',
        'token' => 'your-token'
    ],
    'handler' => 'process_custom_ai_request'
]);
```

## Analytics Integration

### Google Analytics

```php
// Configure GA4
add_filter('aop_analytics_config', function($config) {
    return [
        'provider' => 'google_analytics',
        'measurement_id' => 'G-XXXXXXXXXX',
        'api_secret' => 'your-api-secret'
    ];
});

// Track custom events
aop_track_event([
    'name' => 'optimization_complete',
    'params' => [
        'post_id' => $post_id,
        'score' => $score
    ]
]);
```

### Custom Analytics

```php
// Register custom analytics
add_action('aop_register_analytics', function() {
    aop_register_analytics_provider([
        'name' => 'custom_analytics',
        'track_event' => 'handle_custom_event',
        'track_pageview' => 'handle_custom_pageview'
    ]);
});
```

## Search Engine Integration

### Google Search Console

```php
// Configure GSC
add_filter('aop_search_console_config', function($config) {
    return [
        'site_url' => home_url(),
        'credentials' => json_decode(file_get_contents('path/to/credentials.json')),
        'api_scope' => 'https://www.googleapis.com/auth/webmasters'
    ];
});

// Submit URL for indexing
aop_submit_url_to_index($post_url);
```

### Custom Search Provider

```php
// Register custom search provider
aop_register_search_provider([
    'name' => 'custom_search',
    'submit_url' => 'handle_url_submission',
    'get_status' => 'check_indexing_status'
]);
```

## CDN Integration

### Cloudflare Integration

```php
// Configure Cloudflare
add_filter('aop_cdn_config', function($config) {
    return [
        'provider' => 'cloudflare',
        'zone_id' => 'your-zone-id',
        'api_token' => 'your-api-token'
    ];
});

// Cache purge handling
add_action('aop_after_optimization', function($post_id) {
    aop_purge_cdn_cache([
        'urls' => [get_permalink($post_id)]
    ]);
});
```

## Authentication

### API Key Management

```php
// Register API keys
aop_register_service_key([
    'service' => 'custom_service',
    'key' => 'your-api-key',
    'secret' => 'your-api-secret'
]);
```

### OAuth Integration

```php
// Configure OAuth
add_filter('aop_oauth_config', function($config) {
    return [
        'client_id' => 'your-client-id',
        'client_secret' => 'your-client-secret',
        'redirect_uri' => admin_url('admin.php?page=aop-oauth-callback')
    ];
});
```

## Data Synchronization

### Webhook Handling

```php
// Register webhook handler
add_action('aop_webhook_received', function($payload, $service) {
    if ($service === 'custom_service') {
        process_custom_webhook($payload);
    }
}, 10, 2);
```

### Batch Processing

```php
// Configure batch processing
add_filter('aop_batch_config', function($config) {
    return [
        'batch_size' => 100,
        'delay' => 1,
        'retry_limit' => 3
    ];
});
```

## Error Handling

### Service Errors

```php
// Handle service errors
add_action('aop_service_error', function($error, $service) {
    error_log(sprintf(
        'Service error: %s - %s',
        $service,
        $error->getMessage()
    ));
    
    // Notify admin
    wp_mail(
        get_option('admin_email'),
        'Service Error',
        $error->getMessage()
    );
});
```

### Fallback Handling

```php
// Configure service fallback
add_filter('aop_service_fallback', function($fallback, $service) {
    if ($service === 'ai_service') {
        return 'backup_ai_service';
    }
    return $fallback;
}, 10, 2);
```

## Best Practices

1. **Security**
   - Store credentials securely
   - Use environment variables
   - Implement rate limiting
   - Validate responses

2. **Performance**
   - Cache API responses
   - Use batch operations
   - Implement retry logic
   - Monitor API usage

3. **Maintenance**
   - Monitor service status
   - Log API interactions
   - Update credentials
   - Test integrations

## Troubleshooting

Common integration issues:

1. **Authentication Issues**
   - Verify credentials
   - Check token expiration
   - Review access permissions
   - Monitor API logs

2. **Rate Limiting**
   - Implement backoff
   - Cache responses
   - Monitor usage
   - Use bulk operations

3. **Data Sync Issues**
   - Verify webhook delivery
   - Check data format
   - Monitor sync status
   - Handle conflicts
