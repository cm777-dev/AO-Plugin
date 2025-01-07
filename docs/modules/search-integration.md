# Search Integration Module

The Search Integration module enhances WordPress search capabilities with AI-powered features and advanced ranking algorithms.

## Features

- Enhanced search algorithm
- Custom ranking factors
- Faceted search
- Search analytics
- Query optimization
- AI-powered relevance
- Real-time suggestions

## Configuration

### Basic Setup

1. Navigate to Agent Optimization Pro > Search Integration
2. Enable the module
3. Configure search settings
4. Set up ranking factors

### Search Settings

Configure search parameters:

```php
// Custom search settings
add_filter('aop_search_settings', function($settings) {
    return [
        'min_word_length' => 3,
        'fuzzy_matching' => true,
        'boost_factors' => [
            'title' => 2.0,
            'content' => 1.0,
            'tags' => 1.5
        ]
    ];
});
```

## Search Enhancement

### Custom Search Query

```php
// Enhance search query
add_filter('posts_search', function($search, $query) {
    if ($query->is_search()) {
        $search = aop_enhance_search($search, $query);
    }
    return $search;
}, 10, 2);
```

### Ranking Factors

```php
// Add custom ranking factor
add_filter('aop_ranking_factors', function($factors) {
    $factors['custom_factor'] = [
        'weight' => 1.5,
        'callback' => 'calculate_custom_score'
    ];
    return $factors;
});
```

## Search Analytics

### Tracking Searches

```php
// Track search query
aop_track_search([
    'query' => $search_term,
    'results' => $result_count,
    'user_id' => get_current_user_id()
]);
```

### Analytics Dashboard

Access search analytics:

1. Total searches
2. Popular queries
3. Zero-result searches
4. Click-through rates

## AI Integration

### Query Understanding

```php
// Enhance query understanding
add_filter('aop_query_understanding', function($query) {
    return [
        'intent' => aop_detect_intent($query),
        'entities' => aop_extract_entities($query),
        'context' => aop_get_search_context()
    ];
});
```

### Result Ranking

```php
// Custom result ranking
add_filter('aop_search_ranking', function($posts, $query) {
    return aop_rank_results($posts, [
        'query' => $query,
        'user_context' => aop_get_user_context(),
        'relevance_model' => 'neural'
    ]);
}, 10, 2);
```

## Best Practices

1. **Performance**
   - Enable search caching
   - Optimize index updates
   - Use efficient queries
   - Monitor response times

2. **Relevance**
   - Configure boost factors
   - Update synonyms regularly
   - Train on user feedback
   - Monitor search quality

3. **User Experience**
   - Implement auto-suggest
   - Show related searches
   - Provide clear filters
   - Handle zero results

## Troubleshooting

Common issues and solutions:

1. **Performance Issues**
   - Enable caching
   - Optimize database
   - Review index size
   - Check query complexity

2. **Relevance Problems**
   - Review ranking factors
   - Update synonyms
   - Check boost settings
   - Analyze search logs

## API Reference

### Functions

```php
// Search enhancement
aop_enhance_search($query)
aop_rank_results($posts, $args)
aop_get_suggestions($query)

// Analytics
aop_track_search($data)
aop_get_search_stats($period)
```

### Filters

```php
// Modify search query
add_filter('aop_search_query', function($query) {
    return $query;
});

// Custom ranking
add_filter('aop_result_ranking', function($ranking, $post) {
    return $ranking;
}, 10, 2);
```

### Actions

```php
// Search events
do_action('aop_before_search', $query);
do_action('aop_after_search', $results, $query);

// Analytics events
do_action('aop_search_tracked', $search_data);
```

## Integration Examples

### Custom Post Types

```php
// Include custom post types in search
add_filter('aop_searchable_types', function($types) {
    $types[] = 'product';
    return $types;
});
```

### External Search Services

```php
// Integrate external search
add_filter('aop_external_search', function($results, $query) {
    $external_results = get_external_results($query);
    return array_merge($results, $external_results);
}, 10, 2);
```
