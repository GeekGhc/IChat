<?php

namespace App\WebSocket\Controller;

use App\Utility\APP;
use App\WebSocket\Actions\User\UserInfo;
use App\WebSocket\Actions\User\UserOnline;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;

Class Index extends Base{
    function hello(){
        $this->response()->setMessage('call hello with arg:'. json_encode($this->caller()->getArgs()));
    }

    public function who(){
        $this->response()->setMessage('your fd is '. $this->caller()->getClient()->getFd());
    }

    public function delay(){
        $this->response()->setMessage("this is a delay message");
        $client = $this->caller()->getClient();

        //异步推送
        //TaskManager::async 回调函数的代码是在task进程中去执行的
        TaskManager::async(function ()use ($client){
           $server = ServerManager::getInstance()->getSwooleServer();
           $i = 0;
           while($i<5){
               sleep(1);
               $server->push($client->getFd(),'Push in http at '.time());
               $i++;
           }
        });
    }

    /**
     * 当前用户信息
     */
    function info(){
        $info = $this->currentUser();
        if($info){
            // todo
            $message = new UserInfo();
            Logger::getInstance()->log("info.....");
            Logger::getInstance()->log(json_encode($info));
            $message->setIntro($info['intro']);
            $message->setUserFd($info['userFd']);
            $message->setAvatar($info['avatar']);
            $message->setUsername($info['username']);
            $this->response()->setMessage($message);
        }
    }

    /**
     * 在线用户列表
     * @throws \Exception
     */
    function online(){
        $list = $this->redis()->hGetAll(APP::REDIS_ONLINE_KEY);
        if($list){
            $message = new UserOnline();
            $message->setList($list);
            $this->response()->setMessage($message);
        }
    }

    function heartbeat()
    {
        $this->response()->setMessage('PONG');
    }
}