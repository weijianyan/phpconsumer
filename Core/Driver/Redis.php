<?php

namespace Core\Driver;

class Redis {

    /**
     * @var \Redis 
     */
    private $redis;

    /**
     * @var String 
     */
    private $alias;

    /**
     * @var array 
     */
    static private $_cache = array();

    private function __construct($alias) {
        $config = \Core\Config::get("redis/{$alias}");
        if ($config && is_array($config)) {
            $this->redis = new \Redis();
            $this->redis->connect($config['host'], $config['port'], $config['timeout'], NULL, $config['read_timeout']);
            if (isset($config['password']) && $config['password']) {
                $this->redis->auth($config['password']);
            }
            $this->alias = $alias;
        } else {
            throw new \Core\ConsumerException('Redis配置错误', 1010);
        }
    }

    /**
     * @param string $alias
     * @return \Redis
     */
    static public function instance($alias) {
        if (!static::$_cache[$alias]) {
            static::$_cache[$alias] = new self($alias);
        }
        return static::$_cache[$alias];
    }

    public function __call($name, $arguments) {
        return call_user_func_array(array($this->redis, $name), $arguments);
    }

    /**
     * 关闭REDIS
     */
    public function close() {
        static::$_cache[$this->alias]->close();
        unset(static::$_cache[$this->alias]);
    }

}
