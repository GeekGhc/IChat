<?php

namespace App\WebSocket;

use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\{
    Caller,
    Response
};

Class WebSocketParser implements ParserInterface
{
    /**
     * 解码消息
     * @param $raw
     * @param $client 当前客户端
     * @return Caller|null
     */
    public function decode($raw, $client): ?Caller
    {
        // TODO: Implement decode() method.
        $caller = new Caller();
        //聊天消息 {"controller":"broadcast","action":"roomBroadcast","params":{"content":"111"}}
        if ($raw != 'PING') {
            $payload = json_decode($raw,true);
            $class = isset($payload['controller'])?$payload['controller']:'index';
            $action = isset($payload['action'])?$payload['action']:'actionNotFound';
            $params = isset($payload['params'])?(array)$payload['params']:[];

            $controllerClass = "\\App\\WebSocket\\Controller\\".ucfirst($class);
            if(!class_exists($controllerClass)) $controllerClass = "\\App\\WebSocket\\Controller\\Index";
            $caller->setClient($caller);
            $caller->setControllerClass($controllerClass);
            $caller->setAction($action);
            $caller->setArgs($params);
        }else{
            //如果是心跳测试则直接返回PONG
            $caller->setControllerClass("\\App\\WebSocket\\Controller\\Index");
            $caller->setAction('heartbeat');
        }
        return $caller;
    }


    /**
     * 打包下发消息
     * @param Response $response  控制器返回
     * @param $client
     * @return string|null
     */
    public function encode(Response $response, $client): ?string
    {
        // TODO: Implement encode() method.
        return $response->getMessage();
    }
}