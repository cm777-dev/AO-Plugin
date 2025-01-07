# Structured Data Module

The Structured Data module automatically generates and manages schema.org markup for your WordPress content.

## Features

- Automatic schema.org JSON-LD generation
- Support for multiple schema types
- Custom schema mapping
- Schema validation
- Rich snippet preview

## Configuration

### Basic Setup

1. Navigate to Agent Optimization Pro > Structured Data
2. Enable the module
3. Select default schema types
4. Configure global settings

### Schema Types

The module supports various schema types:

- Article
- Product
- LocalBusiness
- Organization
- Person
- Event
- Recipe
- Custom types

### Custom Schema Mapping

Map your content to schema properties:

```php
add_filter('aop_schema_mapping', function($mapping) {
    $mapping['Article'] = [
        'headline' => 'post_title',
        'datePublished' => 'post_date',
        'author' => [
            'type' => 'Person',
            'name' => 'post_author.display_name'
        ]
    ];
    return $mapping;
});
```

## Usage

### Automatic Generation

The module automatically generates schema for:

- Posts
- Pages
- Custom post types
- Archives
- Author pages

### Manual Implementation

Add schema to specific elements:

```php
// Get schema for a post
$schema = aop_get_schema($post_id);

// Output schema
aop_output_schema($schema);
```

### Schema Validation

Validate your schema:

```php
// Validate schema
$is_valid = aop_validate_schema($schema);

// Get validation errors
$errors = aop_get_schema_errors($schema);
```

## Best Practices

1. **Complete Information**
   - Fill all required fields
   - Provide accurate data
   - Keep information updated

2. **Testing**
   - Use Google's Rich Results Test
   - Monitor search console
   - Check schema validation

3. **Performance**
   - Enable caching
   - Minimize schema size
   - Use appropriate schema types

## Troubleshooting

Common issues and solutions:

1. **Invalid Schema**
   - Check required fields
   - Validate data types
   - Review nested objects

2. **Missing Data**
   - Verify data sources
   - Check mapping configuration
   - Review fallback values

3. **Performance Issues**
   - Enable caching
   - Optimize queries
   - Review schema size

## API Reference

### Functions

```php
// Get schema for a post
aop_get_schema($post_id, $type = null)

// Output schema
aop_output_schema($schema)

// Validate schema
aop_validate_schema($schema)

// Get schema errors
aop_get_schema_errors($schema)
```

### Filters

```php
// Modify schema output
add_filter('aop_schema_output', function($schema, $post_id) {
    return $schema;
}, 10, 2);

// Add custom schema types
add_filter('aop_schema_types', function($types) {
    return $types;
});
```

### Actions

```php
// Before schema generation
do_action('aop_before_schema_generation', $post_id);

// After schema generation
do_action('aop_after_schema_generation', $schema, $post_id);
```
