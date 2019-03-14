<?php
namespace App\Utility;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;

Class Redis{
    use Singleton;
    protected $redis;

    function __construct()
    {
        $this->redis = new \Redis();
        $this->connect();
    }

    function getConnect(): \Redis
    {
        return $this->redis;
    }

    function connect(): Redis
    {
        $conf = Config::getInstance()->getConf("redis");
        $this->redis->connect($conf['host'],$conf['port']);
        $this->redis->setOption(\Redis::OPT_SERIALIZER,\Redis::SERIALIZER_PHP);
        if(!empty($conf['auth'])){
            $this->redis->auth($conf['auth']);
        }
        return $this;
    }
}