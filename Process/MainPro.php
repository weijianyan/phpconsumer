<?php

namespace Process;

/**
 * 主进程
 */
class MainPro extends Process {

    /**
     * 管理进程
     * @var ManagePro 
     */
    private $manage_pro;

    /**
     * 子进程执行的
     */
    protected function dosmth() {
        \Core\Logger::log('启动主进程');
        $this->logPid();
        $this->startManage();
        $this->waitSig();
    }

    /**
     * 记录PID文件
     */
    private function logPid() {
        file_put_contents(\Core\Config::get('config/pid_file'), $this->getPid(), LOCK_EX);
    }

    /**
     * 启动管理进程
     */
    private function startManage() {
        $this->manage_pro = new ManagePro();
    }

    /**
     * 等待信号
     */
    private function waitSig() {
        pcntl_sigprocmask(SIG_BLOCK, array(SIGHUP, SIGTERM));
        while (true) {
            $info = array();
            pcntl_sigwaitinfo(array(SIGHUP, SIGTERM), $info);
            switch ($info['signo']) {
                case SIGHUP:
                    \Core\Logger::log('重新获取配置，重启所有进程');
                    posix_kill($this->manage_pro->getPid(), $info['signo']);
                    break;
                case SIGTERM:
                    posix_kill($this->manage_pro->getPid(), $info['signo']);
                    $status = 0;
                    pcntl_waitpid($this->manage_pro->getPid(), $status);
                    unlink(\Core\Config::get('config/pid_file'));
                    \Core\Logger::log('退出consumer服务');
                    break 2;
                default :
                    break;
            }
        }
    }

}
