<?php
namespace App\WebSocket\Controller;


use App\Task\BroadcastTask;
use App\WebSocket\Actions\Broadcast\BroadcastMessage;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;

class Broadcast extends Controller
{
    /**
     * 系统发送消息
     */
    function roomBroadcast(){
        $client = $this->caller()->getClient();
        $broadcastPayload = $this->caller()->getArgs();
        if(!empty($broadcastPayload) && isset($broadcastPayload['content'])){
            $message = new BroadcastMessage();
            $message->setFromUserFd($client->getFd());
            $message->setContent($broadcastPayload['content']);
            $message->setType($broadcastPayload['type']);
            $message->setSendTime(date('Y-m-d H:i:s'));
            TaskManager::async(new BroadcastTask(['payload'=>$message->__toString(),'fromFd'=>$client->getFd()]));
        }
        $this->response()->setStatus($this->response()::STATUS_OK);
    }
}