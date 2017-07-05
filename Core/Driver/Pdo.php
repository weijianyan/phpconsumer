<?php

namespace Core\Driver;

class Pdo {

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var String 
     */
    private $alias;

    /**
     * @var array 
     */
    static private $_cache = array();

    private function __construct($alias) {
        $config = \Core\Config::get("mysql/{$alias}");
        if ($config && is_array($config)) {
            $this->pdo = new \PDO("mysql:dbname={$config['database']};host={$config['host']}:{$config['port']}", $config['username'], $config['password'], array(\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC));
            $this->alias = $alias;
        } else {
            throw new \Core\ConsumerException('Mysql配置错误', 1011);
        }
    }

    /**
     * @param string $alias
     * @return \PDO
     */
    static public function instance($alias) {
        if (!static::$_cache[$alias]) {
            static::$_cache[$alias] = new self($alias);
        }
        return static::$_cache[$alias];
    }

    public function __call($name, $arguments) {
        return call_user_func_array(array($this->pdo, $name), $arguments);
    }

    /**
     * 关闭
     */
    public function close() {
        unset(static::$_cache[$this->alias]);
    }

}
