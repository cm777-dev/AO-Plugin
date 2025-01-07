<?php
if (!defined('ABSPATH')) {
    exit;
}

// Get optimization statistics
global $wpdb;
$total_posts = wp_count_posts()->publish;
$optimized_posts = $wpdb->get_var(
    "SELECT COUNT(DISTINCT post_id) FROM $wpdb->postmeta 
    WHERE meta_key = '_aop_optimization_data'"
);
$average_score = round($wpdb->get_var(
    "SELECT AVG(meta_value) FROM $wpdb->postmeta 
    WHERE meta_key = '_aop_optimization_score'"
) ?? 0);
?>

<div class="aop-dashboard">
    <div class="aop-stats-grid">
        <div class="aop-stat-card">
            <div class="aop-stat-value"><?php echo esc_html($total_posts); ?></div>
            <div class="aop-stat-label"><?php _e('Total Posts', 'agent-optimization-pro'); ?></div>
        </div>
        
        <div class="aop-stat-card">
            <div class="aop-stat-value"><?php echo esc_html($optimized_posts); ?></div>
            <div class="aop-stat-label"><?php _e('Optimized Posts', 'agent-optimization-pro'); ?></div>
        </div>
        
        <div class="aop-stat-card">
            <div class="aop-stat-value"><?php echo esc_html($average_score); ?>%</div>
            <div class="aop-stat-label"><?php _e('Average Optimization Score', 'agent-optimization-pro'); ?></div>
        </div>
    </div>

    <div class="aop-card">
        <h2><?php _e('Recent Optimizations', 'agent-optimization-pro'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Post Title', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Score', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Last Updated', 'agent-optimization-pro'); ?></th>
                    <th><?php _e('Actions', 'agent-optimization-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recent_posts = get_posts([
                    'post_type' => ['post', 'page'],
                    'posts_per_page' => 10,
                    'meta_key' => '_aop_optimization_data',
                    'orderby' => 'modified',
                    'order' => 'DESC',
                ]);

                foreach ($recent_posts as $post) :
                    $optimization_data = get_post_meta($post->ID, '_aop_optimization_data', true);
                    $score = $optimization_data['optimization_score'] ?? 0;
                ?>
                <tr>
                    <td>
                        <a href="<?php echo get_edit_post_link($post->ID); ?>">
                            <?php echo esc_html($post->post_title); ?>
                        </a>
                    </td>
                    <td>
                        <span class="aop-score-badge" style="background: <?php echo esc_attr(get_score_color($score)); ?>">
                            <?php echo esc_html($score); ?>%
                        </span>
                    </td>
                    <td><?php echo get_the_modified_date('Y-m-d H:i:s', $post); ?></td>
                    <td>
                        <a href="<?php echo get_edit_post_link($post->ID); ?>" class="button button-small">
                            <?php _e('Edit', 'agent-optimization-pro'); ?>
                        </a>
                        <a href="<?php echo get_permalink($post->ID); ?>" class="button button-small" target="_blank">
                            <?php _e('View', 'agent-optimization-pro'); ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="aop-card">
        <h2><?php _e('Top Keywords', 'agent-optimization-pro'); ?></h2>
        <div id="aop-keywords-chart"></div>
    </div>
</div>

<style>
.aop-score-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    color: white;
    font-weight: bold;
}

#aop-keywords-chart {
    height: 300px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Fetch and display keywords chart
    wp.apiRequest({
        path: 'agent-optimization-pro/v1/analytics/visibility',
        method: 'GET'
    }).done(function(response) {
        if (response.top_keywords) {
            const ctx = document.getElementById('aop-keywords-chart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(response.top_keywords),
                    datasets: [{
                        label: 'Keyword Usage',
                        data: Object.values(response.top_keywords),
                        backgroundColor: '#2271b1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    });
});
</script><?php

function get_score_color($score) {
    if ($score >= 80) {
        return '#46b450'; // Green
    } elseif ($score >= 60) {
        return '#ffb900'; // Yellow
    } else {
        return '#dc3232'; // Red
    }
}
