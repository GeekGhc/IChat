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
use EasySwoole\EasySwoole\Logger;

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
//        Logger::getInstance()->log("username = ".$username);
        if($redis instanceof RedisPoolObject){
            $info = self::mockUser($req->fd,$username);
            $redis->hSet(APP::REDIS_ONLINE_KEY,$req->fd,$info);
            $redis->incr(APP::SYSTEM_CON_COUNT_KEY);
            $count = $redis->get(APP::SYSTEM_CON_COUNT_KEY);

            //全频道通知新用户上线

        }else{
            throw new \Exception('Redis pool is empty');
        }
    }

    static function onClose(\swooler\server $server,int $fd,int $reactorId){
        $info = $server->connection_info($fd);
        if($info['websocket_status']!==0){
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

    /**
     * 生产一个游客用户
     * @param int     $userFd
     * @param  string $userName
     * @return array
     */
    static private function mockUser($userFd, $userName)
    {
        mt_srand();
        $introduce = ['请叫我秋名山车神', '这不是去学校的车', '最长的路是你的套路', '车速超快我有点怕', '最美的风景是在路上', '身娇腰柔易推倒', '时光静好与君语', '细水流年与君同', '繁华落尽与君老', '吃瓜什么的最棒了'];
        return ['username' => $userName, 'userFd' => $userFd, 'avatar' => rand(0, 9), 'intro' => $introduce[rand(0, 9)]];
    }

}