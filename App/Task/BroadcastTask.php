<?php
namespace App\Task;

use App\Utility\App;
use App\Utility\Redis;
use App\WebSocket\WebSocketAction;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

Class BroadcastTask extends AbstractAsyncTask{

    /**
     * 任务投递
     * @param $taskData
     * @param $taskId
     * @param $fromWorkerId
     * @param null $flags
     * @return bool
     */
    function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        $redis = Redis::getInstance()->getConnect();
        $users = $redis->hGetAll(App::REDIS_ONLINE_KEY);

        $server = ServerManager::getInstance()->getSwooleServer();
        foreach ($users as $fd=>$userInfo){
            $connection = $server->connection_info($fd);
            //用户正常在线则推送
            if($connection['websocket_status']==3){
                $server->push($fd,$taskData['payload']);
            }
        }
        //添加到离线消息
        $payload = json_decode($taskData['payload'],true);
        if($payload['action']=='103'){
            $userInfo = $redis->hGet(APP::REDIS_ONLINE_KEY,$taskData['fromFd']);
            $payload['fromUserFd'] = 0;//标识系统消息
            $payload['action'] = WebSocketAction::BROADCAST_LAST_MESSAGE;
            $payload['username'] = $userInfo['username'];
            $payload['avatar'] = $userInfo['avatar'];
            $redis->lPush(APP::REDIS_LAST_MESSAGE_KEY,json_encode($payload,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $max = Config::getInstance()->getConf('SYSTEM.LAST_MESSAGE_MAX');
            $redis->lTrim(App::REDIS_LAST_MESSAGE_KEY,0,$max-1);
        }
        return true;
    }
    function finish($result, $task_id)
    {
        // TODO: Implement finish() method.
    }
}