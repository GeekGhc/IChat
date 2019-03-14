<?php
namespace App\WebSocket;

use App\Task\BroadcastTask;
use App\Utility\APP;
use App\Utility\Pool\RedisPool;
use App\Utility\Pool\RedisPoolObject;
use App\WebSocket\Actions\Broadcast\BroadcastAdmin;
use App\WebSocket\Actions\User\UserInRoom;
use App\WebSocket\Actions\User\UserOutRoom;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;

class WebSocketEvents
{
    /**
     * 打开链接时  将用户fd存入Redis
     * @param \swoole_websocket_server $server
     * @param \swoole_http_request $req
     * @throws \Exception
     */
    static function onOpen(\swoole_websocket_server $server,\swoole_http_request $req){
        $redisPool = PoolManager::getInstance()->getPool(RedisPool::class);
        $redis = $redisPool->getObj();
        $username = $req->get['username']??'游客' . str_pad($req->fd, 4, '0', STR_PAD_LEFT);
        if($redis instanceof RedisPoolObject){
            $info = self::mockUser($req->fd,$username);
            $redis->hSet(APP::REDIS_ONLINE_KEY,$req->fd,$info);
            $redis->incr(APP::SYSTEM_CON_COUNT_KEY);
            $count = $redis->get(APP::SYSTEM_CON_COUNT_KEY);

            //全频道通知新用户上线
            $message = new UserInRoom();
            $message->setInfo($info);
            TaskManager::async(new BroadcastTask(['payload'=>$message->__toString(),'fromFd'=>$req->fd]));

            if(empty($req->get['is_reconnection']) || $req->get['is_reconnection']=='0'){
                //发送最后的n条消息
                $last_message_max = Config::getInstance()->getConf('SYSTEM.LAST_MESSAGE_MAX');
                $lastMessage = $redis->lRange(APP::REDIS_LAST_MESSAGE_KEY,0,$last_message_max);
                for($i = count($lastMessage)-1;$i>0;$i--){
                    $server->push($req->fd,$lastMessage[$i]);
                }

                //对用户单独发送欢迎消息
                $runDays = intval((time()-($redis->get(APP::SYSTEM_RUNTIME_KEY)))/86400);
                $message = new BroadcastAdmin();
                $message->setContent("{$username}，系统已稳定运行{$runDays}天，共计服务{$count}人次");
                $server->push($req->fd,$message->__toString());
            }
        }else{
            throw new \Exception('Redis pool is empty');
        }
    }

    /**
     * 关闭用户连接 将用户fd从redis中删除
     * @param \swoole_server $server
     * @param int $fd
     * @param int $reactorId
     * @throws \Exception
     */
    static function onClose(\swoole_server $server,int $fd,int $reactorId){
        $info = $server->connection_info($fd);
        if($info['websocket_status']!==0){
            $redisPool = PoolManager::getInstance()->getPool(RedisPool::class);
            $redis = $redisPool->getObj();
            if($redis instanceof RedisPoolObject){
                $redis->hDel(APP::REDIS_ONLINE_KEY,$fd);

                //通知其他用户已下线
                $message = new UserOutRoom();
                $message->setUserFd($fd);
                TaskManager::async(new BroadcastTask(['payload'=>$message->__toString(),'fromFd'=>$fd]));

                $redisPool->recycleObj($redis);
                echo "websocket user {$fd} was close\n";
            }else{
                throw new \Exception('Redis pool is empty');
            }
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
            $redis->set(APP::SYSTEM_RUNTIME_KEY,time());
            $redis->del(APP::SYSTEM_CON_COUNT_KEY);
            if($redis->exists(APP::REDIS_ONLINE_KEY)){
                $clear =  $redis->del(APP::REDIS_ONLINE_KEY);
                $status = $clear?'success':'failed';
                echo "Redis online user clear {$status}\n";
            }
            $redisPool->recycleObj($redis);
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