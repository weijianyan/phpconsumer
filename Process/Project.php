<?php

namespace Process;

/**
 * 项目
 */
class Project {

    /**
     * 项目名
     * @var string 
     */
    private $project_name;

    /**
     * worker进程列表
     * @var array
     */
    private $worker_list = array();

    public function __construct($project_name) {
        $this->project_name = $project_name;
    }

    /**
     * 启动项目
     */
    public function start() {
        $this->stop();
        $project_config = \Core\Config::get("project/{$this->project_name}");
        if ($project_config && is_array($project_config)) {
            for ($i = 0; $i < $project_config['worker']; $i++) {
                $worker = new WorkerPro($this->project_name);
                $this->worker_list[$worker->getPid()] = $worker;
            }
            \Core\Logger::log("项目 {$this->project_name} 启动");
        } else {
            \Core\Logger::log("项目 {$this->project_name} 配置信息不存在");
        }
    }

    /**
     * 停止worker进程
     */
    public function stop() {
        if ($this->worker_list && is_array($this->worker_list)) {
            foreach ($this->worker_list as $worker) {
                /* @var $worker \Process\WorkerPro */
                posix_kill($worker->getPid(), SIGTERM);
            }
            foreach ($this->worker_list as $worker) {
                $status = 0;
                pcntl_waitpid($worker->getPid(), $status);
            }
            \Core\Logger::log("项目 {$this->project_name} 停止");
        }
        $this->worker_list = array();
        gc_collect_cycles();
    }

    /**
     * 重启项目
     */
    public function restart() {
        $this->stop();
        \Core\Config::clearCache();
        $this->start();
    }

    /**
     * 检查worker进程状态
     * @param int $pid
     */
    public function checkWorker($pid) {
        if ($this->worker_list[$pid]) {
            $status = 0;
            pcntl_waitpid($pid, $status);
            if (pcntl_wifexited($status)) {
                unset($this->worker_list[$pid]);
                $worker = new WorkerPro($this->project_name);
                $this->worker_list[$worker->getPid()] = $worker;
            }
        }
    }

}
