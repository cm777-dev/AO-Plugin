<?php
if (!defined('ABSPATH')) {
    exit;
}

$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'dashboard';
?>

<div class="wrap">
    <h1><?php _e('Agent Optimization Pro', 'agent-optimization-pro'); ?></h1>

    <nav class="nav-tab-wrapper">
        <a href="?page=agent-optimization-pro&tab=dashboard" 
           class="nav-tab <?php echo $active_tab === 'dashboard' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Dashboard', 'agent-optimization-pro'); ?>
        </a>
        <a href="?page=agent-optimization-pro&tab=settings" 
           class="nav-tab <?php echo $active_tab === 'settings' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Settings', 'agent-optimization-pro'); ?>
        </a>
        <a href="?page=agent-optimization-pro&tab=api" 
           class="nav-tab <?php echo $active_tab === 'api' ? 'nav-tab-active' : ''; ?>">
            <?php _e('API Management', 'agent-optimization-pro'); ?>
        </a>
        <a href="?page=agent-optimization-pro&tab=agents" 
           class="nav-tab <?php echo $active_tab === 'agents' ? 'nav-tab-active' : ''; ?>">
            <?php _e('AI Agents', 'agent-optimization-pro'); ?>
        </a>
    </nav>

    <div class="tab-content">
        <?php
        switch ($active_tab) {
            case 'dashboard':
                include __DIR__ . '/dashboard-tab.php';
                break;
            case 'settings':
                include __DIR__ . '/settings-tab.php';
                break;
            case 'api':
                include __DIR__ . '/api-tab.php';
                break;
            case 'agents':
                include __DIR__ . '/agents-tab.php';
                break;
        }
        ?>
    </div>
</div>

<style>
.tab-content {
    margin-top: 20px;
    padding: 20px;
    background: white;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.aop-card {
    background: white;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 15px;
    margin-bottom: 20px;
}

.aop-card h2 {
    margin-top: 0;
    padding-bottom: 12px;
    border-bottom: 1px solid #eee;
}

.aop-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.aop-stat-card {
    background: white;
    border: 1px solid #ccd0d4;
    padding: 20px;
    text-align: center;
}

.aop-stat-value {
    font-size: 36px;
    font-weight: bold;
    color: #1e1e1e;
    margin: 10px 0;
}

.aop-stat-label {
    color: #646970;
    font-size: 14px;
}

.aop-chart-container {
    margin-top: 30px;
}

.form-table th {
    width: 250px;
}
</style>
