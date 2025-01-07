<?php
namespace AgentOptimizationPro\Modules;

class StructuredData {
    public function __construct() {
        add_action('wp_head', [$this, 'output_structured_data']);
        add_action('save_post', [$this, 'generate_structured_data'], 10, 3);
    }

    public function register_routes() {
        register_rest_route('agent-optimization-pro/v1', '/structured-data/(?P<post_id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_structured_data'],
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            },
        ]);
    }

    public function output_structured_data() {
        if (is_singular()) {
            $structured_data = $this->generate_structured_data_for_post(get_the_ID());
            if ($structured_data) {
                echo '<script type="application/ld+json">' . wp_json_encode($structured_data, JSON_UNESCAPED_SLASHES) . '</script>';
            }
        }
    }

    public function generate_structured_data($post_id, $post, $update) {
        if (wp_is_post_revision($post_id)) {
            return;
        }

        $structured_data = $this->generate_structured_data_for_post($post_id);
        update_post_meta($post_id, '_aop_structured_data', $structured_data);
    }

    private function generate_structured_data_for_post($post_id) {
        $post = get_post($post_id);
        if (!$post) {
            return null;
        }

        $structured_data = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title($post),
            'datePublished' => get_the_date('c', $post),
            'dateModified' => get_the_modified_date('c', $post),
            'author' => [
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name', $post->post_author),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $this->get_site_logo_url(),
                ],
            ],
        ];

        // Add featured image if available
        if (has_post_thumbnail($post)) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'full');
            if ($image) {
                $structured_data['image'] = [
                    '@type' => 'ImageObject',
                    'url' => $image[0],
                    'width' => $image[1],
                    'height' => $image[2],
                ];
            }
        }

        return $structured_data;
    }

    private function get_site_logo_url() {
        $custom_logo_id = get_theme_mod('custom_logo');
        if ($custom_logo_id) {
            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
            if ($logo) {
                return $logo[0];
            }
        }
        return '';
    }

    public function get_structured_data($request) {
        $post_id = $request['post_id'];
        $structured_data = get_post_meta($post_id, '_aop_structured_data', true);
        
        if (empty($structured_data)) {
            $structured_data = $this->generate_structured_data_for_post($post_id);
        }

        if (empty($structured_data)) {
            return new \WP_Error(
                'no_structured_data',
                __('No structured data available for this post.', 'agent-optimization-pro'),
                ['status' => 404]
            );
        }

        return new \WP_REST_Response($structured_data, 200);
    }
}
