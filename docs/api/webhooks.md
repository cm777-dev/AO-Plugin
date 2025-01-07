# Webhooks

Agent Optimization Pro supports webhooks to notify external services about events in real-time.

## Overview

Webhooks allow your application to receive real-time updates about events that occur within the plugin.

## Configuration

### Setting Up Webhooks

1. Navigate to Agent Optimization Pro > Settings > Webhooks
2. Click "Add Webhook"
3. Configure:
   - Payload URL
   - Secret key
   - Events to monitor
   - Active status

### Webhook Settings

```php
// Register custom webhook
aop_register_webhook([
    'url' => 'https://your-app.com/webhook',
    'secret' => 'your-secret-key',
    'events' => ['content.updated', 'agent.registered'],
    'active' => true
]);
```

## Events

### Available Events

1. **Content Events**
   - `content.created`
   - `content.updated`
   - `content.deleted`
   - `content.optimized`

2. **Agent Events**
   - `agent.registered`
   - `agent.updated`
   - `agent.deleted`
   - `agent.task.completed`

3. **API Events**
   - `api.endpoint.created`
   - `api.key.generated`
   - `api.key.revoked`

4. **Search Events**
   - `search.performed`
   - `search.zero_results`

### Custom Events

```php
// Register custom event
add_filter('aop_webhook_events', function($events) {
    $events['custom.event'] = [
        'label' => 'Custom Event',
        'description' => 'Triggered when custom action occurs'
    ];
    return $events;
});

// Trigger custom event
do_action('aop_webhook_trigger', 'custom.event', $payload);
```

## Payload Format

### Standard Format

```json
{
    "event": "content.updated",
    "timestamp": "2025-01-06T22:00:00Z",
    "data": {
        "post_id": 123,
        "changes": {
            "title": "New Title",
            "content": "Updated content"
        }
    }
}
```

### Security

Each webhook request includes:

1. Signature header (`X-AOP-Signature`)
2. Timestamp header (`X-AOP-Timestamp`)
3. Event ID header (`X-AOP-Event-ID`)

Verify webhook authenticity:

```php
function verify_webhook($payload, $signature, $secret) {
    $expected = hash_hmac('sha256', $payload, $secret);
    return hash_equals($expected, $signature);
}
```

## Delivery

### Retry Policy

- Initial attempt
- 5 retries with exponential backoff
- Maximum retry window: 24 hours

```php
// Custom retry policy
add_filter('aop_webhook_retry_policy', function($policy) {
    return [
        'max_attempts' => 5,
        'initial_delay' => 30,
        'max_delay' => 3600
    ];
});
```

### Delivery Status

Monitor webhook delivery:

```php
// Get delivery status
$status = aop_get_webhook_delivery_status($webhook_id);

// Get delivery attempts
$attempts = aop_get_webhook_attempts($webhook_id);
```

## Best Practices

1. **Security**
   - Use HTTPS endpoints
   - Validate signatures
   - Rotate secret keys
   - Monitor failed deliveries

2. **Performance**
   - Respond quickly (2xx)
   - Process asynchronously
   - Handle duplicates
   - Monitor payload size

3. **Reliability**
   - Implement retry logic
   - Log delivery attempts
   - Monitor success rates
   - Handle failures gracefully

## API Reference

### Functions

```php
// Webhook management
aop_register_webhook($args)
aop_update_webhook($webhook_id, $args)
aop_delete_webhook($webhook_id)

// Event handling
aop_trigger_webhook($event, $payload)
aop_get_webhook_events()
```

### Filters

```php
// Modify webhook delivery
add_filter('aop_webhook_delivery', function($delivery, $webhook) {
    return $delivery;
}, 10, 2);

// Custom event handling
add_filter('aop_process_webhook_event', function($processed, $event) {
    return $processed;
}, 10, 2);
```

### Actions

```php
// Webhook lifecycle
do_action('aop_webhook_registered', $webhook_id);
do_action('aop_webhook_triggered', $event, $payload);
do_action('aop_webhook_delivered', $webhook_id, $response);
```

## Troubleshooting

Common issues and solutions:

1. **Delivery Failures**
   - Check endpoint availability
   - Verify SSL certificates
   - Review response codes
   - Check payload format

2. **Security Issues**
   - Verify secret keys
   - Check signature calculation
   - Review IP whitelist
   - Monitor suspicious activity
