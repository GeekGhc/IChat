<?php

namespace App\HttpController;

use EasySwoole\Component\AtomicManager;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Trace\Logger;
use Swoole\Atomic;


/**
 * Class Index
 * @package App\HttpController
 */
class Index extends Controller
{
    /**
     * 首页方法
     * @author : evalor <master@evalor.cn>
     */
    function index()
    {
        \EasySwoole\EasySwoole\Logger::getInstance()->log("this is daat","notice");
        $this->response()->withHeader('Content-type', 'text/html;charset=utf-8');
        $this->response()->write('<div style="text-align: center;margin-top: 30px"><h2>欢迎使用EASYSWOOLE</h2></div></br>');
        $this->response()->write('<div style="text-align: center">您现在看到的页面是默认的 Index 控制器的输出</div></br>');
        $this->response()->write('<div style="text-align: center"><a href="https://www.easyswoole.com/Manual/2.x/Cn/_book/Base/http_controller.html">查看手册了解详细使用方法</a></div></br>');
    }

    function test()
    {
        $conf = Config::getInstance()->getConf('redis');
        $data = Config::getInstance()->toArray();
//        $content = file_get_contents(__DIR__."/../Views/websocket.php");
//        $this->response()->write(json_encode($data));

//        TaskManager::async(function(){
//            echo "异步执行了任务";
//            return true;
//        },function(){
//           echo "这里是回调函数";
//        });

        // 多任务并发
//        $tasks[] = function () { sleep(3);return 'this is 1'; }; // 任务1
//        $tasks[] = function () { sleep(2);return 'this is 2'; };     // 任务2
//        $tasks[] = function () { sleep(5);return 'this is 3'; }; // 任务3
//
//        $results = \EasySwoole\EasySwoole\Swoole\Task\TaskManager::barrier($tasks, 3);
//        var_dump($results);


        // 注册一个atomic对象
//        AtomicManager::getInstance()->add('second');
//        $atomic = AtomicManager::getInstance()->get('second');
//        $atomic->add(5);
//        $this->response()->write($atomic->get());

        $container = new \EasySwoole\Component\Container();
        $container->set("OnOpen",function (){
            echo "onOpen事件回调";
        });
        $container->set("name",'仙士可');
        call_user_func($container->get("OnOpen"));

    }
}
