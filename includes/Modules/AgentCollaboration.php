<?php
namespace AgentOptimizationPro\Modules;

class AgentCollaboration {
    private $namespace = 'agent-optimization-pro/v1';

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
        add_action('init', [$this, 'register_agent_post_type']);
        add_action('admin_init', [$this, 'schedule_agent_tasks']);
    }

    public function register_agent_post_type() {
        register_post_type('aop_agent', [
            'labels' => [
                'name' => __('AI Agents', 'agent-optimization-pro'),
                'singular_name' => __('AI Agent', 'agent-optimization-pro'),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'agent-optimization-pro',
            'supports' => ['title', 'editor', 'custom-fields'],
            'capability_type' => 'post',
            'capabilities' => [
                'create_posts' => 'manage_options',
            ],
        ]);
    }

    public function register_routes() {
        // Agent registration endpoint
        register_rest_route($this->namespace, '/agents/register', [
            'methods' => 'POST',
            'callback' => [$this, 'register_agent'],
            'permission_callback' => [$this, 'verify_agent_key'],
            'args' => [
                'agent_name' => [
                    'required' => true,
                    'type' => 'string',
                ],
                'capabilities' => [
                    'required' => true,
                    'type' => 'array',
                ],
                'callback_url' => [
                    'required' => true,
                    'type' => 'string',
                    'format' => 'uri',
                ],
            ],
        ]);

        // Agent interaction endpoint
        register_rest_route($this->namespace, '/agents/interact', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_agent_interaction'],
            'permission_callback' => [$this, 'verify_agent_key'],
            'args' => [
                'agent_id' => [
                    'required' => true,
                    'type' => 'string',
                ],
                'action' => [
                    'required' => true,
                    'type' => 'string',
                ],
                'data' => [
                    'required' => true,
                    'type' => 'object',
                ],
            ],
        ]);
    }

    public function register_agent($request) {
        $agent_name = sanitize_text_field($request['agent_name']);
        $capabilities = $request['capabilities'];
        $callback_url = esc_url_raw($request['callback_url']);

        // Create agent post
        $agent_id = wp_insert_post([
            'post_type' => 'aop_agent',
            'post_title' => $agent_name,
            'post_status' => 'publish',
            'meta_input' => [
                '_aop_agent_capabilities' => $capabilities,
                '_aop_agent_callback_url' => $callback_url,
                '_aop_agent_key' => wp_generate_password(32, false),
                '_aop_agent_status' => 'active',
            ],
        ]);

        if (is_wp_error($agent_id)) {
            return new \WP_Error('agent_registration_failed', 'Failed to register agent', ['status' => 500]);
        }

        return rest_ensure_response([
            'agent_id' => $agent_id,
            'agent_key' => get_post_meta($agent_id, '_aop_agent_key', true),
        ]);
    }

    public function handle_agent_interaction($request) {
        $agent_id = $request['agent_id'];
        $action = $request['action'];
        $data = $request['data'];

        // Verify agent exists and is active
        $agent = get_post($agent_id);
        if (!$agent || $agent->post_type !== 'aop_agent') {
            return new \WP_Error('invalid_agent', 'Invalid agent ID', ['status' => 404]);
        }

        $agent_status = get_post_meta($agent_id, '_aop_agent_status', true);
        if ($agent_status !== 'active') {
            return new \WP_Error('inactive_agent', 'Agent is not active', ['status' => 403]);
        }

        // Process the interaction based on the action
        switch ($action) {
            case 'query_content':
                return $this->handle_content_query($data);
            case 'update_metadata':
                return $this->handle_metadata_update($data);
            case 'suggest_optimization':
                return $this->handle_optimization_suggestion($data);
            default:
                return new \WP_Error('invalid_action', 'Invalid action specified', ['status' => 400]);
        }
    }

    private function handle_content_query($data) {
        $args = [
            'post_type' => 'post',
            'posts_per_page' => $data['limit'] ?? 10,
            'post_status' => 'publish',
        ];

        if (!empty($data['query'])) {
            $args['s'] = sanitize_text_field($data['query']);
        }

        $query = new \WP_Query($args);
        $posts = array_map([$this, 'format_post_for_agent'], $query->posts);

        return rest_ensure_response([
            'posts' => $posts,
            'total' => $query->found_posts,
        ]);
    }

    private function handle_metadata_update($data) {
        if (empty($data['post_id']) || empty($data['metadata'])) {
            return new \WP_Error('invalid_data', 'Missing required data', ['status' => 400]);
        }

        $post_id = intval($data['post_id']);
        $metadata = $data['metadata'];

        foreach ($metadata as $key => $value) {
            update_post_meta($post_id, '_aop_agent_' . sanitize_key($key), $value);
        }

        return rest_ensure_response([
            'success' => true,
            'post_id' => $post_id,
        ]);
    }

    private function handle_optimization_suggestion($data) {
        if (empty($data['post_id']) || empty($data['suggestions'])) {
            return new \WP_Error('invalid_data', 'Missing required data', ['status' => 400]);
        }

        $post_id = intval($data['post_id']);
        $suggestions = $data['suggestions'];

        // Store suggestions
        update_post_meta($post_id, '_aop_agent_suggestions', $suggestions);

        // Notify admin
        $this->notify_admin_of_suggestions($post_id, $suggestions);

        return rest_ensure_response([
            'success' => true,
            'post_id' => $post_id,
        ]);
    }

    private function format_post_for_agent($post) {
        return [
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'date' => $post->post_date,
            'modified' => $post->post_modified,
            'metadata' => $this->get_agent_metadata($post->ID),
        ];
    }

    private function get_agent_metadata($post_id) {
        global $wpdb;
        $metadata = $wpdb->get_results($wpdb->prepare(
            "SELECT meta_key, meta_value FROM $wpdb->postmeta 
            WHERE post_id = %d AND meta_key LIKE '_aop_agent_%'",
            $post_id
        ));

        $formatted_metadata = [];
        foreach ($metadata as $meta) {
            $key = str_replace('_aop_agent_', '', $meta->meta_key);
            $formatted_metadata[$key] = $meta->meta_value;
        }

        return $formatted_metadata;
    }

    public function verify_agent_key($request) {
        $auth_header = $request->get_header('X-Agent-Key');
        if (!$auth_header) {
            return false;
        }

        global $wpdb;
        $agent_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM $wpdb->postmeta 
            WHERE meta_key = '_aop_agent_key' AND meta_value = %s",
            $auth_header
        ));

        return !empty($agent_exists);
    }

    private function notify_admin_of_suggestions($post_id, $suggestions) {
        $admin_email = get_option('admin_email');
        $post_title = get_the_title($post_id);
        $subject = sprintf(__('New AI Agent Suggestions for: %s', 'agent-optimization-pro'), $post_title);
        
        $message = "New optimization suggestions have been received for the post: $post_title\n\n";
        foreach ($suggestions as $suggestion) {
            $message .= "- " . $suggestion['message'] . "\n";
        }
        
        wp_mail($admin_email, $subject, $message);
    }

    public function schedule_agent_tasks() {
        if (!wp_next_scheduled('aop_daily_agent_cleanup')) {
            wp_schedule_event(time(), 'daily', 'aop_daily_agent_cleanup');
        }
    }
}
