<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Trace\Logger;


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
        $content = file_get_contents(__DIR__."/../Views/websocket.php");
        $this->response()->write($content);
    }
}
