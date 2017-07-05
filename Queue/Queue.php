<?php

namespace Queue;

abstract class Queue {

    /**
     * @var array 
     */
    protected $queue_config;

    /**
     * @var array 
     */
    static private $_cache = array();

    private function __construct($queue_config) {
        $this->queue_config = $queue_config;
    }

    abstract public function pop();

    abstract public function close();

    /**
     * @param string $alias
     * @return \Queue
     */
    static public function instance($alias) {
        if (!static::$_cache[$alias]) {
            $queue_config = \Core\Config::get("queue/{$alias}");
            if ($queue_config && is_array($queue_config)) {
                switch ($queue_config['type']) {
                    case 'redis':
                        static::$_cache[$alias] = new \Queue\Redis($queue_config);
                        break;
                    case 'kafka':
                        static::$_cache[$alias] = new \Queue\Kafka($queue_config);
                        break;
                    default:
                        throw new \Core\ConsumerException('不明队列类型', 1200);
                }
            } else {
                throw new \Core\ConsumerException('队列配置不存在', 1200);
            }
        }
        return static::$_cache[$alias];
    }

}
