<?php
namespace App\WebSocket\Controller;

use App\Task\BroadcastTask;
use App\Task\NotifyTask;
use App\WebSocket\Actions\Broadcast\BroadcastMessage;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\Client\WebSocket as WebSocketClient;

class Broadcast extends Controller
{
    /**
     * 系统发送消息
     */
    function roomBroadcast(){
        /**
         * @var WebSocketClient $client;
         */
        $client = $this->caller()->getClient();
        $broadcastPayload = $this->caller()->getArgs();
        if(!empty($broadcastPayload) && isset($broadcastPayload['content'])){
            $message = new BroadcastMessage;
            $message->setFromUserFd($client->getFd());
            $message->setContent($broadcastPayload['content']);
            $message->setType($broadcastPayload['type']);
            $message->setSendTime(date('Y-m-d H:i:s'));
            Logger::getInstance()->log(print_r($message->__toString(),1));
            TaskManager::async(new BroadcastTask(['payload'=>$message->__toString(),'fromFd'=>$client->getFd()]),function(){});
        }
        $this->response()->setStatus($this->response()::STATUS_OK);
    }
}