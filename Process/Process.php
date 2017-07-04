<?php

namespace Process;

abstract class Process {

    private $pid;

    public function __construct() {
        $pid = pcntl_fork();
        if ($pid > 0) {
            $this->pid = $pid;
        } elseif (0 == $pid) {
            $this->pid = posix_getpid();
            $this->dosmth();
            exit();
        }
    }

    public function getPid() {
        return $this->pid;
    }

    /**
     * 子进程执行的
     */
    abstract protected function dosmth();
}
