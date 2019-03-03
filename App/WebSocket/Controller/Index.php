<?php

namespace App\WebSocket\Controller;

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
}