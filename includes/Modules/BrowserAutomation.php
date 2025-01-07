<?php
namespace AgentOptimizationPro\Modules;

class BrowserAutomation {
    private $browser_use_path;
    private $python_executable;
    
    public function __construct() {
        $this->browser_use_path = plugin_dir_path(__FILE__) . 'browser-use';
        $this->python_executable = $this->get_python_executable();
        add_action('init', [$this, 'init']);
    }

    public function init() {
        $this->check_dependencies();
        $this->register_endpoints();
    }

    private function check_dependencies() {
        if (!file_exists($this->browser_use_path)) {
            $this->install_browser_use();
        }
    }

    private function install_browser_use() {
        // Clone browser-use repository
        $command = sprintf(
            'git clone %s %s',
            'https://github.com/browser-use/browser-use.git',
            $this->browser_use_path
        );
        exec($command);

        // Install Python dependencies
        $pip_command = sprintf(
            '%s -m pip install -r %s/requirements.txt',
            $this->python_executable,
            $this->browser_use_path
        );
        exec($pip_command);
    }

    private function get_python_executable() {
        if (defined('PYTHON_EXECUTABLE')) {
            return PYTHON_EXECUTABLE;
        }
        return 'python';
    }

    public function register_endpoints() {
        add_action('rest_api_init', function() {
            register_rest_route('aop/v1', '/browser/execute', [
                'methods' => 'POST',
                'callback' => [$this, 'execute_browser_action'],
                'permission_callback' => [$this, 'check_permission'],
                'args' => [
                    'action' => [
                        'required' => true,
                        'type' => 'string',
                        'enum' => ['navigate', 'click', 'type', 'screenshot', 'extract']
                    ],
                    'params' => [
                        'required' => true,
                        'type' => 'object'
                    ]
                ]
            ]);
        });
    }

    public function check_permission() {
        return current_user_can('manage_options');
    }

    public function execute_browser_action($request) {
        $action = $request->get_param('action');
        $params = $request->get_param('params');

        $script_path = sprintf(
            '%s/examples/%s_example.py',
            $this->browser_use_path,
            $action
        );

        if (!file_exists($script_path)) {
            return new \WP_Error(
                'invalid_action',
                'Invalid browser action specified',
                ['status' => 400]
            );
        }

        $command = sprintf(
            '%s %s %s',
            $this->python_executable,
            $script_path,
            escapeshellarg(json_encode($params))
        );

        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            return new \WP_Error(
                'execution_failed',
                'Browser action execution failed',
                ['status' => 500]
            );
        }

        return [
            'success' => true,
            'data' => implode("\n", $output)
        ];
    }
}
