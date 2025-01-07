<?php
namespace AgentOptimizationPro\Modules;

class ApiGenerator {
    private $namespace = 'agent-optimization-pro/v1';

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        // Register dynamic endpoints from database
        $this->register_dynamic_endpoints();

        // Register configuration endpoints
        register_rest_route($this->namespace, '/api-config', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_api_config'],
                'permission_callback' => [$this, 'admin_permissions_check'],
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'create_api_endpoint'],
                'permission_callback' => [$this, 'admin_permissions_check'],
                'args' => [
                    'endpoint_name' => [
                        'required' => true,
                        'type' => 'string',
                    ],
                    'endpoint_path' => [
                        'required' => true,
                        'type' => 'string',
                    ],
                    'endpoint_method' => [
                        'required' => true,
                        'type' => 'string',
                        'enum' => ['GET', 'POST', 'PUT', 'DELETE'],
                    ],
                    'endpoint_config' => [
                        'required' => true,
                        'type' => 'object',
                    ],
                ],
            ],
        ]);
    }

    private function register_dynamic_endpoints() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aop_api_endpoints';
        $endpoints = $wpdb->get_results("SELECT * FROM $table_name WHERE is_active = 1");

        foreach ($endpoints as $endpoint) {
            register_rest_route($this->namespace, $endpoint->endpoint_path, [
                'methods' => $endpoint->endpoint_method,
                'callback' => [$this, 'handle_dynamic_endpoint'],
                'permission_callback' => [$this, 'api_permissions_check'],
                'args' => json_decode($endpoint->endpoint_config, true),
            ]);
        }
    }

    public function handle_dynamic_endpoint($request) {
        $params = $request->get_params();
        $endpoint_path = str_replace($this->namespace, '', $request->get_route());
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'aop_api_endpoints';
        $endpoint = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE endpoint_path = %s",
            $endpoint_path
        ));

        if (!$endpoint) {
            return new \WP_Error('endpoint_not_found', 'Endpoint configuration not found', ['status' => 404]);
        }

        $config = json_decode($endpoint->endpoint_config, true);
        return $this->process_endpoint_request($params, $config);
    }

    private function process_endpoint_request($params, $config) {
        // Process the request based on configuration
        $response = [];
        
        if (!empty($config['post_type'])) {
            $args = [
                'post_type' => $config['post_type'],
                'posts_per_page' => $config['posts_per_page'] ?? 10,
                'post_status' => 'publish',
            ];

            if (!empty($params['search'])) {
                $args['s'] = sanitize_text_field($params['search']);
            }

            $query = new \WP_Query($args);
            $response['items'] = array_map([$this, 'format_post_for_api'], $query->posts);
            $response['total'] = $query->found_posts;
            $response['pages'] = $query->max_num_pages;
        }

        return rest_ensure_response($response);
    }

    private function format_post_for_api($post) {
        $post_data = [
            'id' => $post->ID,
            'title' => get_the_title($post),
            'content' => get_the_content(null, false, $post),
            'excerpt' => get_the_excerpt($post),
            'date' => get_the_date('c', $post),
            'modified' => get_the_modified_date('c', $post),
            'slug' => $post->post_name,
            'link' => get_permalink($post),
            'featured_image' => get_the_post_thumbnail_url($post, 'full'),
            'author' => [
                'id' => $post->post_author,
                'name' => get_the_author_meta('display_name', $post->post_author),
            ],
        ];

        // Add structured data
        $structured_data = get_post_meta($post->ID, '_aop_structured_data', true);
        if ($structured_data) {
            $post_data['structured_data'] = $structured_data;
        }

        return $post_data;
    }

    public function admin_permissions_check() {
        return current_user_can('manage_options');
    }

    public function api_permissions_check() {
        // Check API key if provided
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : '';
        if ($api_key) {
            return $this->validate_api_key($api_key);
        }
        
        // Default to logged-in users only
        return is_user_logged_in();
    }

    private function validate_api_key($api_key) {
        $valid_keys = get_option('aop_api_keys', []);
        return in_array($api_key, $valid_keys);
    }
}
