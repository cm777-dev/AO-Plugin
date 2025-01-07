# API Authentication

This guide covers the authentication methods supported by Agent Optimization Pro's API.

## Authentication Methods

### API Key Authentication

The primary method of authentication is via API keys.

```bash
curl -X GET \
  https://your-site.com/wp-json/aop/v1/endpoint \
  -H 'Authorization: Bearer your-api-key'
```

### OAuth 2.0

For more secure applications, OAuth 2.0 is supported:

1. Register your application
2. Obtain client credentials
3. Implement OAuth flow
4. Use access tokens

```php
// Example OAuth configuration
define('AOP_OAUTH_CLIENT_ID', 'your-client-id');
define('AOP_OAUTH_CLIENT_SECRET', 'your-client-secret');
```

### JWT Authentication

JSON Web Tokens are supported for stateless authentication:

```php
// Generate JWT token
$token = aop_generate_jwt([
    'user_id' => 1,
    'scope' => ['read', 'write']
]);

// Use token in request
$response = wp_remote_get('https://your-site.com/wp-json/aop/v1/endpoint', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token
    ]
]);
```

## Managing API Keys

### Generation

1. Navigate to Agent Optimization Pro > API Keys
2. Click "Generate New Key"
3. Set permissions and expiration
4. Save the key securely

### Permissions

API keys can have different permission levels:

- `read` - Read-only access
- `write` - Create and update access
- `delete` - Full access including deletion
- `admin` - Administrative access

```php
// Check key permissions
if (aop_key_has_permission($key, 'write')) {
    // Perform write operation
}
```

### Rotation

Best practices for key rotation:

1. Generate new key
2. Update applications
3. Test new key
4. Revoke old key

## Security Best Practices

### Key Storage

```php
// Never store keys in code
define('AOP_API_KEY', getenv('AOP_API_KEY'));

// Use WordPress options for runtime storage
update_option('aop_api_key', $encrypted_key, true);
```

### Rate Limiting

```php
// Configure rate limits
add_filter('aop_rate_limits', function($limits) {
    return [
        'per_ip' => 1000,
        'per_key' => 5000,
        'window' => 3600
    ];
});
```

### IP Whitelisting

```php
// Add IP whitelist
add_filter('aop_ip_whitelist', function($ips) {
    $ips[] = '192.168.1.1';
    return $ips;
});
```

## Error Handling

### Authentication Errors

Common error responses:

```json
{
    "code": "invalid_key",
    "message": "Invalid API key provided",
    "status": 401
}
```

```json
{
    "code": "insufficient_permissions",
    "message": "Key does not have required permissions",
    "status": 403
}
```

### Troubleshooting

1. **Invalid Key**
   - Verify key is correct
   - Check key hasn't expired
   - Ensure key is active

2. **Permission Denied**
   - Check key permissions
   - Verify endpoint requirements
   - Review access logs

## API Reference

### Functions

```php
// Key management
aop_generate_api_key($args)
aop_validate_api_key($key)
aop_revoke_api_key($key)

// Permission checking
aop_key_has_permission($key, $permission)
aop_get_key_permissions($key)
```

### Filters

```php
// Modify key generation
add_filter('aop_api_key_args', function($args) {
    return $args;
});

// Custom validation
add_filter('aop_validate_key', function($is_valid, $key) {
    return $is_valid;
}, 10, 2);
```

### Actions

```php
// Key lifecycle events
do_action('aop_api_key_generated', $key);
do_action('aop_api_key_revoked', $key);

// Authentication events
do_action('aop_auth_success', $key);
do_action('aop_auth_failure', $key, $reason);
```
