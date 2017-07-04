<?php

namespace Core;

class Logger {

    /**
     * 日志文件
     * @var string 
     */
    static private $log_file;

    /**
     * 得到日志文件
     * @return string
     */
    static private function getLogFile() {
        if (!static::$log_file) {
            static::$log_file = \Core\Config::get('config/log_file');
        }
        return static::$log_file;
    }

    /**
     * 记录日志
     * @param string $msg
     */
    static public function log($msg) {
        $time = date('Y-m-d H:i:s', time());
        $msg = "{$time}: {$msg}\n";
        error_log($msg, 3, static::getLogFile());
    }

}
