<?php

namespace Queue;

abstract class Queue {

    /**
     * @var array 
     */
    protected $queue_config;

    public function __construct($queue_alias) {
        $queue_config = \Core\Config::get("queue/{$queue_alias}");
        if ($queue_config && is_array($queue_config)) {
            $this->queue_config = $queue_config;
        } else {
            throw new \Core\ConsumerException('队列配置不存在', 1100);
        }
    }

    abstract public function pull();
}
