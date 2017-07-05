<?php

namespace Queue;

class Redis extends Queue {

    public function pop() {
        $redis = \Core\Driver\Redis::instance($this->queue_config['source']);
        $data = $redis->rPop($this->queue_config['key']);
    }

    public function close() {
        
    }

}
