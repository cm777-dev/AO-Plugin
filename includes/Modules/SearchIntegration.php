<?php
namespace AgentOptimizationPro\Modules;

class SearchIntegration {
    public function __construct() {
        add_action('init', [$this, 'register_sitemap']);
        add_action('wp_head', [$this, 'add_agent_meta_tags']);
        add_action('rest_api_init', [$this, 'register_routes']);
        add_filter('robots_txt', [$this, 'modify_robots_txt'], 10, 2);
    }

    public function register_sitemap() {
        if (!is_admin()) {
            add_filter('wp_sitemaps_posts_query_args', [$this, 'modify_sitemap_query']);
            add_filter('wp_sitemaps_posts', [$this, 'add_agent_metadata_to_sitemap']);
        }
    }

    public function modify_sitemap_query($args) {
        // Ensure agent-optimized content is prioritized
        $args['meta_query'] = [
            [
                'key' => '_aop_optimization_score',
                'type' => 'NUMERIC',
                'compare' => 'EXISTS',
            ],
        ];
        $args['orderby'] = [
            'meta_value_num' => 'DESC',
            'date' => 'DESC',
        ];
        
        return $args;
    }

    public function add_agent_metadata_to_sitemap($post_urls) {
        foreach ($post_urls as &$url) {
            $post_id = url_to_postid($url['loc']);
            if ($post_id) {
                $optimization_data = get_post_meta($post_id, '_aop_optimization_data', true);
                if ($optimization_data) {
                    $url['agent_metadata'] = $optimization_data;
                }
            }
        }
        return $post_urls;
    }

    public function add_agent_meta_tags() {
        if (is_singular()) {
            $post_id = get_the_ID();
            $optimization_data = get_post_meta($post_id, '_aop_optimization_data', true);
            
            if ($optimization_data) {
                echo "\n<!-- Agent Discovery Meta Tags -->\n";
                echo '<meta name="agent-optimization-score" content="' . esc_attr($optimization_data['optimization_score']) . '" />' . "\n";
                echo '<meta name="agent-keywords" content="' . esc_attr($optimization_data['keywords']) . '" />' . "\n";
                echo '<meta name="agent-description" content="' . esc_attr($optimization_data['agent_description']) . '" />' . "\n";
                echo '<link rel="agent-api-endpoint" href="' . esc_url(rest_url('agent-optimization-pro/v1')) . '" />' . "\n";
            }
        }
    }

    public function modify_robots_txt($output, $public) {
        if ('0' == $public) {
            return $output;
        }

        $output .= "\n# Agent Optimization Pro - Agent Crawler Rules\n";
        $output .= "User-agent: *\n";
        $output .= "Allow: /wp-json/agent-optimization-pro/\n";
        $output .= "Allow: /*.json$\n";
        
        // Add sitemap location
        $output .= "\nSitemap: " . home_url('/wp-sitemap.xml') . "\n";
        
        return $output;
    }

    public function register_routes() {
        register_rest_route('agent-optimization-pro/v1', '/search/agent', [
            'methods' => 'GET',
            'callback' => [$this, 'handle_agent_search'],
            'permission_callback' => '__return_true',
            'args' => [
                'query' => [
                    'required' => true,
                    'type' => 'string',
                ],
                'type' => [
                    'required' => false,
                    'type' => 'string',
                    'default' => 'semantic',
                ],
            ],
        ]);

        register_rest_route('agent-optimization-pro/v1', '/analytics/visibility', [
            'methods' => 'GET',
            'callback' => [$this, 'get_visibility_analytics'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ]);
    }

    public function handle_agent_search($request) {
        $query = sanitize_text_field($request['query']);
        $type = $request['type'];

        switch ($type) {
            case 'semantic':
                return $this->semantic_search($query);
            case 'keyword':
                return $this->keyword_search($query);
            default:
                return new \WP_Error('invalid_search_type', 'Invalid search type specified', ['status' => 400]);
        }
    }

    private function semantic_search($query) {
        global $wpdb;

        // Get all posts with optimization data
        $posts = get_posts([
            'post_type' => ['post', 'page'],
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => '_aop_optimization_data',
                    'compare' => 'EXISTS',
                ],
            ],
        ]);

        $results = [];
        foreach ($posts as $post) {
            $optimization_data = get_post_meta($post->ID, '_aop_optimization_data', true);
            $score = $this->calculate_semantic_similarity($query, $optimization_data['agent_description']);
            
            if ($score > 0.5) { // Threshold for relevance
                $results[] = [
                    'post' => $this->format_post_for_search($post),
                    'score' => $score,
                ];
            }
        }

        // Sort by relevance score
        usort($results, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return rest_ensure_response($results);
    }

    private function keyword_search($query) {
        $args = [
            'post_type' => ['post', 'page'],
            'post_status' => 'publish',
            's' => $query,
            'meta_query' => [
                [
                    'key' => '_aop_optimization_data',
                    'compare' => 'EXISTS',
                ],
            ],
        ];

        $search_query = new \WP_Query($args);
        $results = array_map([$this, 'format_post_for_search'], $search_query->posts);

        return rest_ensure_response($results);
    }

    private function calculate_semantic_similarity($query, $text) {
        // Simplified semantic similarity calculation
        // In a production environment, you might want to use a more sophisticated NLP approach
        $query_words = explode(' ', strtolower($query));
        $text_words = explode(' ', strtolower($text));
        
        $common_words = array_intersect($query_words, $text_words);
        $similarity = count($common_words) / (log(count($query_words)) + log(count($text_words)));
        
        return min(1, $similarity);
    }

    private function format_post_for_search($post) {
        $optimization_data = get_post_meta($post->ID, '_aop_optimization_data', true);
        
        return [
            'id' => $post->ID,
            'title' => $post->post_title,
            'excerpt' => $post->post_excerpt,
            'url' => get_permalink($post),
            'optimization_score' => $optimization_data['optimization_score'] ?? 0,
            'keywords' => $optimization_data['keywords'] ?? '',
            'agent_description' => $optimization_data['agent_description'] ?? '',
            'last_modified' => get_the_modified_date('c', $post),
        ];
    }

    public function get_visibility_analytics() {
        global $wpdb;

        // Get optimization statistics
        $stats = [
            'total_posts' => wp_count_posts()->publish,
            'optimized_posts' => $wpdb->get_var(
                "SELECT COUNT(DISTINCT post_id) FROM $wpdb->postmeta 
                WHERE meta_key = '_aop_optimization_data'"
            ),
            'average_score' => $wpdb->get_var(
                "SELECT AVG(meta_value) FROM $wpdb->postmeta 
                WHERE meta_key = '_aop_optimization_score'"
            ),
            'top_keywords' => $this->get_top_keywords(),
        ];

        return rest_ensure_response($stats);
    }

    private function get_top_keywords() {
        global $wpdb;
        
        $keywords = $wpdb->get_col(
            "SELECT meta_value FROM $wpdb->postmeta 
            WHERE meta_key = '_aop_optimization_data'"
        );

        $all_keywords = [];
        foreach ($keywords as $keyword_json) {
            $data = json_decode($keyword_json, true);
            if (isset($data['keywords'])) {
                $keywords_array = explode(',', $data['keywords']);
                foreach ($keywords_array as $keyword) {
                    $keyword = trim($keyword);
                    if (!empty($keyword)) {
                        $all_keywords[] = $keyword;
                    }
                }
            }
        }

        $keyword_counts = array_count_values($all_keywords);
        arsort($keyword_counts);
        
        return array_slice($keyword_counts, 0, 10, true);
    }
}
