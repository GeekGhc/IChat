<?php
/**
 * Created by PhpStorm.
 * User: gehuachun
 * Date: 2019-03-03
 * Time: 22:57
 */

namespace App\WebSocket;


use App\Utility\App;
use App\Utility\Pool\RedisPool;
use App\Utility\Pool\RedisPoolObject;
use EasySwoole\Component\Pool\PoolManager;

class WebSocketEvents
{
    /**
     * 打开链接时  将用户fd存入Redis
     * @param \swoole\server $server
     * @param \swoole_http_request $req
     * @throws \Exception
     */
    static function onOpen(\swoole\server $server,\swoole_http_request $req){
        $redisPool = PoolManager::getInstance()->getPool(RedisPool::class);
        $redis = $redisPool->getObj();
        $username = $req->get['username']??'游客' . str_pad($req->fd, 4, '0', STR_PAD_LEFT);
        if($redis instanceof RedisPoolObject){

        }else{
            throw new \Exception('Redis pool is empty');
        }
    }

    static function onClose(\swooler\server $server,int $fd,int $reactorId){
        $info = $server->connection_info();
        if($info['ebsocket_status']!==0){
            $redisPool = PoolManager::getInstance()->getPool(RedisPool::class);
            $redis = $redisPool->getObj();
        }
    }

    /**
     * 清空在线用户列表
     * @throws \Exception
     */
    static function cleanOnlineUsers(){
        $redisPool = PoolManager::getInstance()->getPool(RedisPool::class);
        $redis = $redisPool->getObj();
        if ($redis instanceof RedisPoolObject) {
            $redis->set(App::SYSTEM_RUNTIME_KEY,time());
            $redis->del(APP::SYSTEM_CON_COUNT_KEY);
        }else{
            throw new \Exception('Redis pool is empty');
        }

    }

}