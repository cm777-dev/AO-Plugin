<?php
namespace AgentOptimizationPro\Modules;

class ContentOptimizer {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_optimization_meta_box']);
        add_action('save_post', [$this, 'save_optimization_data']);
        add_filter('the_content', [$this, 'enhance_content']);
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function add_optimization_meta_box() {
        add_meta_box(
            'aop_content_optimization',
            __('Agent Optimization', 'agent-optimization-pro'),
            [$this, 'render_meta_box'],
            ['post', 'page'],
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        wp_nonce_field('aop_content_optimization', 'aop_content_optimization_nonce');
        $optimization_data = get_post_meta($post->ID, '_aop_optimization_data', true);
        
        // Get optimization suggestions
        $suggestions = $this->analyze_content($post->post_content);
        
        include AOP_PLUGIN_DIR . 'includes/Admin/views/optimization-meta-box.php';
    }

    public function save_optimization_data($post_id) {
        if (!isset($_POST['aop_content_optimization_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['aop_content_optimization_nonce'], 'aop_content_optimization')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $optimization_data = [
            'keywords' => sanitize_text_field($_POST['aop_keywords'] ?? ''),
            'agent_description' => sanitize_textarea_field($_POST['aop_agent_description'] ?? ''),
            'optimization_score' => intval($_POST['aop_optimization_score'] ?? 0),
        ];

        update_post_meta($post_id, '_aop_optimization_data', $optimization_data);
    }

    public function enhance_content($content) {
        if (!is_singular()) {
            return $content;
        }

        $post_id = get_the_ID();
        $optimization_data = get_post_meta($post_id, '_aop_optimization_data', true);

        if (empty($optimization_data)) {
            return $content;
        }

        // Add semantic HTML enhancements
        $enhanced_content = $this->add_semantic_markup($content);
        
        // Add agent-specific metadata
        $enhanced_content = $this->add_agent_metadata($enhanced_content, $optimization_data);

        return $enhanced_content;
    }

    private function analyze_content($content) {
        $suggestions = [];
        $settings = get_option('aop_content_optimization_settings');

        // Check content length
        $word_count = str_word_count(strip_tags($content));
        if ($word_count < $settings['min_content_length']) {
            $suggestions[] = [
                'type' => 'warning',
                'message' => sprintf(
                    __('Content length (%d words) is below the recommended minimum (%d words).', 'agent-optimization-pro'),
                    $word_count,
                    $settings['min_content_length']
                )
            ];
        }

        // Check readability
        if ($settings['enable_readability_check']) {
            $readability_score = $this->calculate_readability_score($content);
            $suggestions[] = [
                'type' => $readability_score > 60 ? 'success' : 'warning',
                'message' => sprintf(
                    __('Readability score: %d/100. %s', 'agent-optimization-pro'),
                    $readability_score,
                    $readability_score > 60 ? 'Good job!' : 'Consider simplifying your content.'
                )
            ];
        }

        // Generate keyword suggestions
        if ($settings['enable_keyword_suggestions']) {
            $keywords = $this->extract_keywords($content);
            $suggestions[] = [
                'type' => 'info',
                'message' => __('Suggested keywords: ', 'agent-optimization-pro') . implode(', ', $keywords)
            ];
        }

        return $suggestions;
    }

    private function calculate_readability_score($content) {
        // Simplified Flesch-Kincaid readability score calculation
        $text = strip_tags($content);
        $words = str_word_count($text);
        $sentences = preg_match_all('/[.!?]+/', $text, $matches);
        $syllables = $this->count_syllables($text);

        if ($words === 0 || $sentences === 0) {
            return 0;
        }

        return 206.835 - 1.015 * ($words / $sentences) - 84.6 * ($syllables / $words);
    }

    private function count_syllables($text) {
        // Simplified syllable counting
        $text = strtolower($text);
        $text = preg_replace('/[^a-z]/', '', $text);
        $syllables = 0;
        $vowels = ['a', 'e', 'i', 'o', 'u', 'y'];
        
        for ($i = 0; $i < strlen($text); $i++) {
            if (in_array($text[$i], $vowels)) {
                if ($i === 0 || !in_array($text[$i-1], $vowels)) {
                    $syllables++;
                }
            }
        }
        
        return $syllables;
    }

    private function extract_keywords($content) {
        $text = strtolower(strip_tags($content));
        $words = str_word_count($text, 1);
        $stop_words = $this->get_stop_words();
        $word_freq = array_count_values(array_diff($words, $stop_words));
        arsort($word_freq);
        
        return array_slice(array_keys($word_freq), 0, 5);
    }

    private function get_stop_words() {
        return ['the', 'be', 'to', 'of', 'and', 'a', 'in', 'that', 'have', 'i', 'it', 'for', 'not', 'on', 'with', 'he', 'as', 'you', 'do', 'at'];
    }

    private function add_semantic_markup($content) {
        // Add schema.org article markup
        $content = '<article itemscope itemtype="http://schema.org/Article">' . $content . '</article>';
        
        // Add semantic headings and sections
        $content = preg_replace('/<h([1-6])>(.+?)<\/h([1-6])>/i', '<h$1 itemprop="headline">$2</h$1>', $content);
        
        return $content;
    }

    private function add_agent_metadata($content, $optimization_data) {
        // Add agent-specific metadata as HTML comments
        $agent_meta = "\n<!-- agent-optimization-meta\n" .
            json_encode($optimization_data, JSON_PRETTY_PRINT) .
            "\n-->\n";
            
        return $agent_meta . $content;
    }

    public function register_routes() {
        register_rest_route('agent-optimization-pro/v1', '/optimize/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'optimize_content'],
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            },
            'args' => [
                'id' => [
                    'validate_callback' => function($param) {
                        return is_numeric($param);
                    }
                ],
            ],
        ]);
    }

    public function optimize_content($request) {
        $post_id = $request->get_param('id');
        $post = get_post($post_id);
        
        if (!$post) {
            return new \WP_Error('post_not_found', 'Post not found', ['status' => 404]);
        }

        $suggestions = $this->analyze_content($post->post_content);
        return rest_ensure_response([
            'post_id' => $post_id,
            'suggestions' => $suggestions,
        ]);
    }
}
