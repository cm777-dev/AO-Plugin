# Getting Started

This guide will help you install and configure Agent Optimization Pro for your WordPress site.

## Installation

1. Download the latest version from [GitHub Releases](https://github.com/cm777-dev/AO-Plugin/releases)
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the downloaded zip file and click "Install Now"
5. After installation, click "Activate"

## Initial Configuration

1. Navigate to Agent Optimization Pro in your WordPress admin menu
2. Complete the initial setup wizard:
   - Configure basic settings
   - Set up API access
   - Choose enabled modules
   - Configure structured data preferences

## Quick Start Guide

### 1. Structured Data Setup

```php
// Example: Add custom schema type
add_filter('aop_schema_types', function($types) {
    $types['LocalBusiness'] = [
        'name' => 'Local Business',
        'fields' => [
            'address' => 'text',
            'telephone' => 'text',
            'openingHours' => 'text'
        ]
    ];
    return $types;
});
```

### 2. API Endpoint Creation

1. Go to Agent Optimization Pro > API Generator
2. Click "Add New Endpoint"
3. Configure:
   - Endpoint path
   - HTTP method
   - Response format
   - Access controls

### 3. Content Optimization

1. Edit any post or page
2. Look for the "Content Optimization" meta box
3. Enter your target keywords
4. Follow the optimization suggestions

## Next Steps

- Explore the [Modules](./modules/index.md) documentation
- Learn about [API Integration](./api/index.md)
- Review [Best Practices](./best-practices.md)
- Check out [Advanced Configuration](./configuration.md)
