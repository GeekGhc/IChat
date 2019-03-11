<?php
namespace App\WebSocket\Controller;

use App\Utility\App;
use App\Utility\Pool\RedisPool;
use App\Utility\Pool\RedisPoolObject;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\Socket\AbstractInterface\Controller;


Class Base extends Controller{
    private $redis;

    /**
     * 获取Redis对象
     * @return RedisPoolObject|mixed|null
     * @throws \Exception
     */
    public function redis(){
        if(!($this->redis instanceof RedisPoolObject)){
            $redisPoolObject = PoolManager::getInstance()->getPool(RedisPool::class)->getObj(0.1);
            if($redisPoolObject instanceof RedisPoolObject){
                $this->redis = $redisPoolObject;
            }else{
                throw new \Exception("Get redis failed");
            }
        }
        return $this->redis;
    }

    /**
     * 获取当前用户
     * @return mixed
     */
    public function currentUser(){
        $client = $this->caller()->getClient();
        return $this->redis->hGet(App::REDIS_ONLINE_KEY,$client->getFd());
    }

    /**
     * 回收Redis对象
     */
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        if($this->redis instanceof RedisPoolObject){
            PoolManager::getInstance()->getPool(RedisPool::class)->recycleObj($this->redis);
        }
    }


}