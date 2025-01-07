# API Generator Module

The API Generator module allows you to create and manage custom REST API endpoints for your WordPress site.

## Features

- Dynamic API endpoint creation
- Automatic documentation generation
- Built-in authentication
- Rate limiting
- Response caching
- Versioning support

## Configuration

### Basic Setup

1. Navigate to Agent Optimization Pro > API Generator
2. Enable the module
3. Configure global API settings
4. Create your first endpoint

### Authentication Methods

Supported authentication methods:

- API Keys
- OAuth 2.0
- JWT
- WordPress nonce
- Custom authentication

### Rate Limiting

Configure rate limiting:

```php
// Custom rate limits
add_filter('aop_rate_limits', function($limits) {
    return [
        'per_ip' => 1000,
        'per_key' => 5000,
        'window' => 3600 // 1 hour
    ];
});
```

## Creating Endpoints

### Basic Endpoint

```php
// Register custom endpoint
aop_register_endpoint([
    'path' => '/custom/endpoint',
    'methods' => 'GET',
    'callback' => function($request) {
        return [
            'status' => 'success',
            'data' => 'Your data here'
        ];
    }
]);
```

### Advanced Endpoint

```php
// Advanced endpoint with validation
aop_register_endpoint([
    'path' => '/items/(?P<id>\d+)',
    'methods' => ['GET', 'POST'],
    'callback' => 'handle_item_request',
    'permission_callback' => 'check_item_permission',
    'args' => [
        'id' => [
            'required' => true,
            'validate_callback' => 'is_numeric'
        ]
    ]
]);
```

## Response Handling

### Standard Response Format

```json
{
    "success": true,
    "data": {},
    "message": "Operation successful"
}
```

### Error Handling

```php
// Custom error response
function custom_error_handler($error) {
    return new WP_Error(
        'custom_error',
        'Error message',
        ['status' => 400]
    );
}
```

## Documentation Generation

### Automatic Documentation

The module automatically generates:

- OpenAPI specification
- Endpoint documentation
- Request/response examples
- Authentication guides

### Custom Documentation

```php
// Add custom documentation
add_filter('aop_api_docs', function($docs) {
    $docs['custom_section'] = [
        'title' => 'Custom API Features',
        'content' => 'Documentation content'
    ];
    return $docs;
});
```

## Best Practices

1. **Security**
   - Always validate input
   - Use appropriate authentication
   - Implement rate limiting
   - Sanitize responses

2. **Performance**
   - Enable response caching
   - Optimize database queries
   - Use pagination
   - Implement request throttling

3. **Versioning**
   - Use semantic versioning
   - Maintain backwards compatibility
   - Document breaking changes
   - Provide migration guides

## Troubleshooting

Common issues and solutions:

1. **Authentication Issues**
   - Verify API keys
   - Check permissions
   - Review error logs
   - Test with Postman

2. **Performance Problems**
   - Enable caching
   - Review rate limits
   - Optimize queries
   - Monitor server resources

## API Reference

### Functions

```php
// Register endpoint
aop_register_endpoint($args)

// Validate request
aop_validate_request($request)

// Generate response
aop_generate_response($data)

// Check rate limit
aop_check_rate_limit($key)
```

### Filters

```php
// Modify endpoint registration
add_filter('aop_endpoint_registration', function($args) {
    return $args;
});

// Custom rate limiting
add_filter('aop_rate_limit_rules', function($rules) {
    return $rules;
});
```

### Actions

```php
// Before API request
do_action('aop_before_api_request', $request);

// After API response
do_action('aop_after_api_response', $response, $request);
```
