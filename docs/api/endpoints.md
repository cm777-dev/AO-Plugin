# API Endpoints

Complete reference for all API endpoints provided by Agent Optimization Pro.

## Base URL

All API endpoints are prefixed with:
```
https://your-site.com/wp-json/aop/v1/
```

## Authentication

All endpoints require authentication. See [Authentication](authentication.md) for details.

## Structured Data Endpoints

### Get Schema
```http
GET /schema/{post_id}
```

Parameters:
- `post_id` (integer, required) - WordPress post ID

Response:
```json
{
    "success": true,
    "data": {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "Post Title",
        "datePublished": "2025-01-06T22:00:00Z"
    }
}
```

### Update Schema
```http
POST /schema/{post_id}
```

Parameters:
- `post_id` (integer, required) - WordPress post ID
- `schema` (object, required) - Schema.org JSON-LD object

## Content Optimization

### Analyze Content
```http
POST /analyze
```

Request Body:
```json
{
    "content": "Content to analyze",
    "options": {
        "seo": true,
        "readability": true
    }
}
```

Response:
```json
{
    "success": true,
    "data": {
        "seo_score": 85,
        "readability_score": 78,
        "suggestions": []
    }
}
```

### Get Optimization Suggestions
```http
GET /suggestions/{post_id}
```

Parameters:
- `post_id` (integer, required) - WordPress post ID

## Agent Collaboration

### Register Agent
```http
POST /agents/register
```

Request Body:
```json
{
    "name": "Custom Agent",
    "capabilities": ["analyze", "optimize"],
    "callback_url": "https://agent.example.com/callback"
}
```

### Agent Communication
```http
POST /agents/{agent_id}/communicate
```

Parameters:
- `agent_id` (string, required) - Agent identifier

Request Body:
```json
{
    "type": "task",
    "action": "analyze",
    "data": {}
}
```

## Search Integration

### Search Content
```http
GET /search
```

Parameters:
- `q` (string, required) - Search query
- `type` (string) - Post type filter
- `page` (integer) - Page number
- `per_page` (integer) - Results per page

### Get Search Analytics
```http
GET /search/analytics
```

Parameters:
- `start_date` (string) - Start date (YYYY-MM-DD)
- `end_date` (string) - End date (YYYY-MM-DD)

## API Management

### List Endpoints
```http
GET /endpoints
```

Response:
```json
{
    "success": true,
    "data": {
        "endpoints": [
            {
                "path": "/schema/{post_id}",
                "methods": ["GET", "POST"],
                "description": "Manage schema for posts"
            }
        ]
    }
}
```

### Create Custom Endpoint
```http
POST /endpoints
```

Request Body:
```json
{
    "path": "/custom/endpoint",
    "methods": ["GET"],
    "callback": "custom_callback_function",
    "args": {
        "custom_arg": {
            "required": true,
            "type": "string"
        }
    }
}
```

## Error Responses

All endpoints follow this error format:

```json
{
    "success": false,
    "error": {
        "code": "error_code",
        "message": "Human readable error message",
        "status": 400
    }
}
```

Common error codes:
- `invalid_request` - Malformed request
- `not_found` - Resource not found
- `unauthorized` - Authentication required
- `forbidden` - Insufficient permissions
- `validation_error` - Invalid input data

## Rate Limiting

Headers included in all responses:
```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1704591600
```

## Versioning

The API uses semantic versioning. Breaking changes will result in a new version number.

To use a specific version:
```http
GET /wp-json/aop/v2/endpoint
```

## Pagination

For endpoints returning lists:

Parameters:
- `page` (integer) - Page number
- `per_page` (integer) - Items per page (max 100)

Response Headers:
```
X-WP-Total: 100
X-WP-TotalPages: 10
```

## CORS

Cross-Origin Resource Sharing is supported with proper configuration:

```php
add_filter('aop_cors_origins', function($origins) {
    $origins[] = 'https://trusted-domain.com';
    return $origins;
});
```
