<?php

declare(ticks = 1);

namespace Process;

/**
 * worker进程
 */
class WorkerPro extends Process {

    /**
     * 项目名
     * @var string 
     */
    private $project_name;

    public function __construct($project_name) {
        $this->project_name = $project_name;
        parent::__construct();
    }

    /**
     * 子进程执行的
     */
    protected function dosmth() {
        \Core\Logger::log("一个worker进程 {$this->getPid()} {$this->project_name} 启动");
        $this->installSignalHandler();
        $project_config = \Core\Config::get("project/{$this->project_name}");
        if (is_array($project_config) && $project_config) {
            $exec_path = $project_config['exec_path'];
            $exit_flag = $project_config['exec_args'] ? explode(' ', $project_config['exec_args']) : array();
            while (true) {
                pcntl_exec($exec_path, $exit_flag);
                \Core\Logger::log("一个worker进程 {$this->getPid()} {$this->project_name} 执行一次");
                if (isset($GLOBALS['exit_flag']) && $GLOBALS['exit_flag']) {
                    \Core\Logger::log("一个worker进程 {$this->getPid()} {$this->project_name} 退出");
                    break;
                }
            }
        }
    }

    /**
     * 信号处理器
     */
    private function installSignalHandler() {
        pcntl_signal(SIGTERM, function ($signo) {
            $GLOBALS['exit_flag'] = true;
        });
    }

}
