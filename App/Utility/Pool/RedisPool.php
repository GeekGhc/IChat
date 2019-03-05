<?php
namespace App\Utility\Pool;

use EasySwoole\Component\Pool\AbstractPool;
use EasySwoole\EasySwoole\Config;

class RedisPool extends AbstractPool
{
    protected function createObject()
    {
        // TODO: Implement createObject() method.
        $redis = new RedisPoolObject();
        $conf = Config::getInstance()->getConf('redis');
        $redis->connect($conf['host'],$conf['port']);
        $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        if (!empty($conf['auth'])) {
            $redis->auth($conf['auth']);
        }
        return $redis;
    }

}