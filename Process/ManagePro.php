<?php

namespace Process;

/**
 * 管理进程
 */
class ManagePro extends Process {

    /**
     * 项目列表
     * @var array 
     */
    private $project_list = array();

    /**
     * 子进程执行的
     */
    protected function dosmth() {
        $this->startAllProject();
        $this->waitSig();
    }

    /**
     * 启动所有项目
     */
    private function startAllProject() {
        $config = \Core\Config::get('project');
        $config = is_array($config) ? array_keys($config) : array();
        foreach ($config as $project_name) {
            $this->project_list[$project_name] = new Project($project_name);
            $this->project_list[$project_name]->start();
        }
    }

    /**
     * 停止所有项目
     */
    private function stopAllProject() {
        foreach ($this->project_list as $project) {
            /* @var $project \Process\Project */
            $project->stop();
        }
        $this->project_list = array();
    }

    /**
     * 重启所有项目
     */
    private function restartAllProject() {
        $this->stopAllProject();
        \Core\Config::clearCache();
        $this->startAllProject();
        gc_collect_cycles();
    }

    /**
     * 等待信号
     */
    private function waitSig() {
        pcntl_sigprocmask(SIG_BLOCK, array(SIGHUP, SIGTERM, SIGCHLD));
        while (true) {
            $info = array();
            pcntl_sigwaitinfo(array(SIGHUP, SIGTERM, SIGCHLD), $info);
            switch ($info['signo']) {
                case SIGHUP:
                    $this->restartAllProject();
                    break;
                case SIGTERM:
                    $this->stopAllProject();
                    break 2;
                case SIGCHLD:
                    \Core\Logger::log("一个worker进程 {$info['pid']} 自动退出");
                    foreach ($this->project_list as $project) {
                        /* @var $project \Process\Project */
                        $project->checkWorker($info['pid']);
                    }
                    break;
                default :
                    break;
            }
        }
    }

}
