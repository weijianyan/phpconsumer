<?php

namespace Core;

/**
 * 配置信息获取类
 */
class Config {

    /**
     * 缓存
     * @param array $_cache
     */
    static private $_cache = array();

    /**
     * 获取配置
     * @param string $name
     * @return mixed
     */
    static public function get($name = 'config') {
        $parse = explode('/', $name);
        $file_name = array_shift($parse);
        static::prepare($file_name);
        $rst = & static::$_cache[$file_name];
        foreach ($parse as $val) {
            $rst = & $rst[$val];
        }
        return $rst;
    }

    /**
     * 预取配置文件
     * @param string $name
     */
    static private function prepare($name) {
        if (!isset(static::$_cache[$name])) {
            static::$_cache[$name] = parse_ini_file(PATH . "/Config/{$name}.ini", true);
        }
    }

    /**
     * 清楚所有缓存的配置
     */
    static public function clearCache() {
        static::$_cache = array();
    }

}
