# Basic Configuration

This guide covers the essential configuration settings for Agent Optimization Pro.

## General Settings

### Plugin Activation

After installing the plugin, navigate to `Settings > Agent Optimization Pro` in your WordPress admin panel to configure the basic settings:

1. **License Key**: Enter your license key to activate premium features
2. **Environment**: Choose between Production and Development
3. **Debug Mode**: Enable for detailed logging (development only)

### Module Selection

Enable or disable specific modules based on your needs:

- [ ] Structured Data Generator
- [ ] API Management
- [ ] Content Optimization
- [ ] Agent Collaboration
- [ ] Search Enhancement

### API Configuration

1. **API Authentication**
   - Generate API keys
   - Set access permissions
   - Configure rate limits

2. **Endpoint Settings**
   - Choose base URL path
   - Enable/disable specific endpoints
   - Set response formats

## Performance Settings

### Caching

```php
// Example: Configure cache duration
define('AOP_CACHE_DURATION', 3600); // 1 hour
```

- **Object Cache**: Enable/disable object caching
- **API Response Cache**: Set cache duration
- **Schema Cache**: Configure structured data caching

### Rate Limiting

- **API Requests**: Set maximum requests per minute
- **Agent Interactions**: Configure concurrent agent limits
- **Content Analysis**: Set analysis frequency limits

## Security Settings

### Access Control

- Configure user role permissions
- Set up API access restrictions
- Enable/disable features per user role

### Data Protection

- Enable/disable data collection
- Configure data retention periods
- Set up backup schedules

## Notification Settings

- Configure email notifications
- Set up webhook notifications
- Enable/disable admin notifications

## Save Changes

Remember to click "Save Changes" after modifying any settings. Some changes may require a cache flush or plugin reload.
