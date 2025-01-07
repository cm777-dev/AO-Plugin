# API Reference

This section provides detailed information about the Agent Optimization Pro API endpoints and integration options.

## Authentication

All API requests require authentication using an API key. You can generate API keys in the WordPress admin panel under Agent Optimization Pro > Settings > API Keys.

```bash
curl -X GET \
  https://your-site.com/wp-json/aop/v1/endpoint \
  -H 'Authorization: Bearer your-api-key'
```

## Available Endpoints

### Structured Data

#### Get Schema
```http
GET /wp-json/aop/v1/schema/{post_id}
```

#### Update Schema
```http
POST /wp-json/aop/v1/schema/{post_id}
```

### Content Optimization

#### Analyze Content
```http
POST /wp-json/aop/v1/analyze
```

#### Get Suggestions
```http
GET /wp-json/aop/v1/suggestions/{post_id}
```

### Agent Collaboration

#### Register Agent
```http
POST /wp-json/aop/v1/agents/register
```

#### Agent Communication
```http
POST /wp-json/aop/v1/agents/communicate
```

## Response Formats

All API responses follow this structure:

```json
{
  "success": true,
  "data": {
    // Response data here
  },
  "message": "Operation successful"
}
```

## Error Handling

Errors follow this format:

```json
{
  "success": false,
  "error": {
    "code": "error_code",
    "message": "Human readable error message"
  }
}
```

## Rate Limiting

- 1000 requests per hour per API key
- Rate limit headers included in responses
- Burst limit of 100 requests per minute

## Webhooks

Configure webhooks to receive real-time updates:

1. Navigate to Agent Optimization Pro > Settings > Webhooks
2. Add webhook URL
3. Select events to monitor
4. Configure secret key for validation

## Example Implementation

```php
// PHP example using WordPress HTTP API
$response = wp_remote_post('https://your-site.com/wp-json/aop/v1/analyze', [
    'headers' => [
        'Authorization' => 'Bearer your-api-key',
        'Content-Type' => 'application/json'
    ],
    'body' => json_encode([
        'content' => 'Content to analyze'
    ])
]);

$data = json_decode(wp_remote_retrieve_body($response), true);
```
