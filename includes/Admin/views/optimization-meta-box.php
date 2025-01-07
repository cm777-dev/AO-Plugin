<?php
if (!defined('ABSPATH')) {
    exit;
}

$optimization_data = $optimization_data ?? [];
$keywords = $optimization_data['keywords'] ?? '';
$agent_description = $optimization_data['agent_description'] ?? '';
$optimization_score = $optimization_data['optimization_score'] ?? 0;
?>

<div class="aop-meta-box">
    <div class="aop-score-container">
        <div class="aop-score" style="background: <?php echo esc_attr($this->get_score_color($optimization_score)); ?>">
            <span class="aop-score-value"><?php echo esc_html($optimization_score); ?></span>
            <span class="aop-score-label"><?php _e('Agent Score', 'agent-optimization-pro'); ?></span>
        </div>
    </div>

    <div class="aop-suggestions">
        <h4><?php _e('Optimization Suggestions', 'agent-optimization-pro'); ?></h4>
        <?php if (!empty($suggestions)) : ?>
            <ul class="aop-suggestions-list">
                <?php foreach ($suggestions as $suggestion) : ?>
                    <li class="aop-suggestion-item aop-suggestion-<?php echo esc_attr($suggestion['type']); ?>">
                        <?php echo esc_html($suggestion['message']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p><?php _e('No suggestions available. Save the post to generate suggestions.', 'agent-optimization-pro'); ?></p>
        <?php endif; ?>
    </div>

    <div class="aop-optimization-fields">
        <div class="aop-field">
            <label for="aop_keywords"><?php _e('Agent Keywords', 'agent-optimization-pro'); ?></label>
            <input type="text" id="aop_keywords" name="aop_keywords" value="<?php echo esc_attr($keywords); ?>" 
                   placeholder="<?php esc_attr_e('Enter keywords, separated by commas', 'agent-optimization-pro'); ?>" />
            <p class="description"><?php _e('Keywords help AI agents understand your content focus.', 'agent-optimization-pro'); ?></p>
        </div>

        <div class="aop-field">
            <label for="aop_agent_description"><?php _e('Agent Description', 'agent-optimization-pro'); ?></label>
            <textarea id="aop_agent_description" name="aop_agent_description" rows="3" 
                      placeholder="<?php esc_attr_e('Describe your content for AI agents', 'agent-optimization-pro'); ?>"
            ><?php echo esc_textarea($agent_description); ?></textarea>
            <p class="description"><?php _e('A clear description helps AI agents better understand and categorize your content.', 'agent-optimization-pro'); ?></p>
        </div>

        <input type="hidden" name="aop_optimization_score" value="<?php echo esc_attr($optimization_score); ?>" />
    </div>

    <div class="aop-actions">
        <button type="button" class="button button-secondary" id="aop-analyze-content">
            <?php _e('Analyze Content', 'agent-optimization-pro'); ?>
        </button>
        <span class="spinner"></span>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#aop-analyze-content').on('click', function() {
        var $button = $(this);
        var $spinner = $button.next('.spinner');
        
        $button.prop('disabled', true);
        $spinner.addClass('is-active');

        wp.apiRequest({
            path: 'agent-optimization-pro/v1/optimize/' + $('#post_ID').val(),
            method: 'POST',
        })
        .done(function(response) {
            if (response.suggestions) {
                var $list = $('.aop-suggestions-list');
                $list.empty();
                
                response.suggestions.forEach(function(suggestion) {
                    $list.append(
                        $('<li>')
                            .addClass('aop-suggestion-item')
                            .addClass('aop-suggestion-' + suggestion.type)
                            .text(suggestion.message)
                    );
                });
            }
        })
        .fail(function(response) {
            alert('Error analyzing content: ' + response.responseJSON.message);
        })
        .always(function() {
            $button.prop('disabled', false);
            $spinner.removeClass('is-active');
        });
    });
});
</script>

<style>
.aop-meta-box {
    padding: 12px;
}

.aop-score-container {
    text-align: center;
    margin-bottom: 20px;
}

.aop-score {
    display: inline-block;
    padding: 15px;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    text-align: center;
    color: white;
}

.aop-score-value {
    display: block;
    font-size: 24px;
    font-weight: bold;
    line-height: 1;
    margin-top: 10px;
}

.aop-score-label {
    display: block;
    font-size: 12px;
    margin-top: 5px;
}

.aop-suggestions {
    margin-bottom: 20px;
}

.aop-suggestions-list {
    margin: 0;
}

.aop-suggestion-item {
    margin: 5px 0;
    padding: 8px 12px;
    border-left: 4px solid #ccc;
    background: #f8f8f8;
}

.aop-suggestion-warning {
    border-left-color: #ffb900;
}

.aop-suggestion-success {
    border-left-color: #46b450;
}

.aop-suggestion-info {
    border-left-color: #00a0d2;
}

.aop-optimization-fields {
    margin-bottom: 20px;
}

.aop-field {
    margin-bottom: 15px;
}

.aop-field label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
}

.aop-field input[type="text"],
.aop-field textarea {
    width: 100%;
}

.aop-actions {
    display: flex;
    align-items: center;
}

.aop-actions .spinner {
    float: none;
    margin: 0 10px;
}
</style><?php

function get_score_color($score) {
    if ($score >= 80) {
        return '#46b450'; // Green
    } elseif ($score >= 60) {
        return '#ffb900'; // Yellow
    } else {
        return '#dc3232'; // Red
    }
}
