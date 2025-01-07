# Browser Automation Module

The Browser Automation module integrates the powerful browser-use Python library into Agent Optimization Pro, enabling automated browser interactions and web scraping capabilities.

## Features

- Browser automation and control
- Web scraping and data extraction
- Screenshot capture
- Form interaction
- Navigation control

## Requirements

- Python 3.7 or higher
- Chrome/Chromium browser
- ChromeDriver (automatically managed)

## Configuration

Add the following to your `wp-config.php` to customize the Python executable path:

```php
define('PYTHON_EXECUTABLE', '/path/to/python');
```

## API Endpoints

### Execute Browser Action

```http
POST /wp-json/aop/v1/browser/execute
```

Parameters:
- `action` (string, required) - The browser action to execute
  - `navigate` - Navigate to a URL
  - `click` - Click an element
  - `type` - Type text into an input
  - `screenshot` - Capture screenshot
  - `extract` - Extract data from page
- `params` (object, required) - Action-specific parameters

#### Example: Navigate to URL

```json
{
    "action": "navigate",
    "params": {
        "url": "https://example.com"
    }
}
```

#### Example: Click Element

```json
{
    "action": "click",
    "params": {
        "selector": "#submit-button"
    }
}
```

#### Example: Type Text

```json
{
    "action": "type",
    "params": {
        "selector": "#search-input",
        "text": "search query"
    }
}
```

#### Example: Take Screenshot

```json
{
    "action": "screenshot",
    "params": {
        "output_path": "screenshot.png",
        "full_page": true
    }
}
```

#### Example: Extract Data

```json
{
    "action": "extract",
    "params": {
        "selectors": {
            "title": "h1",
            "price": ".product-price",
            "description": ".product-description"
        }
    }
}
```

## PHP Integration

```php
// Execute browser action programmatically
$browser_module = new \AgentOptimizationPro\Modules\BrowserAutomation();
$result = $browser_module->execute_browser_action([
    'action' => 'navigate',
    'params' => ['url' => 'https://example.com']
]);
```

## Security

The module implements several security measures:

1. **Permission Control**
   - Only administrators can execute browser actions
   - API endpoints require authentication

2. **Input Validation**
   - Action parameters are strictly validated
   - URLs and selectors are sanitized

3. **Resource Management**
   - Browser instances are properly managed
   - Timeouts prevent hanging operations

## Error Handling

The module provides detailed error information:

```json
{
    "code": "execution_failed",
    "message": "Browser action execution failed",
    "data": {
        "status": 500,
        "details": "Error details here"
    }
}
```

## Best Practices

1. **Resource Management**
   - Close browser sessions after use
   - Implement proper error handling
   - Use appropriate timeouts

2. **Performance**
   - Cache frequently accessed data
   - Minimize browser operations
   - Use batch operations when possible

3. **Security**
   - Validate all input
   - Sanitize URLs and selectors
   - Implement rate limiting

## Examples

### Basic Usage

```php
// Navigate and take screenshot
$params = [
    'action' => 'screenshot',
    'params' => [
        'url' => 'https://example.com',
        'output_path' => 'screenshot.png'
    ]
];

$result = wp_remote_post(rest_url('aop/v1/browser/execute'), [
    'headers' => [
        'Content-Type' => 'application/json',
        'X-WP-Nonce' => wp_create_nonce('wp_rest')
    ],
    'body' => json_encode($params)
]);
```

### Data Extraction

```php
// Extract product data
$params = [
    'action' => 'extract',
    'params' => [
        'url' => 'https://example.com/product',
        'selectors' => [
            'title' => '.product-title',
            'price' => '.product-price',
            'description' => '.product-description'
        ]
    ]
];

$result = wp_remote_post(rest_url('aop/v1/browser/execute'), [
    'headers' => [
        'Content-Type' => 'application/json',
        'X-WP-Nonce' => wp_create_nonce('wp_rest')
    ],
    'body' => json_encode($params)
]);
```

## Troubleshooting

Common issues and solutions:

1. **Python Not Found**
   - Ensure Python is installed
   - Set PYTHON_EXECUTABLE in wp-config.php
   - Check system PATH

2. **Browser Launch Failed**
   - Verify Chrome/Chromium installation
   - Check ChromeDriver compatibility
   - Review system resources

3. **Permission Issues**
   - Check WordPress user roles
   - Verify file permissions
   - Review API authentication
