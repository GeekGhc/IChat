<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Process\HotReload;
use App\WebSocket\WebSocketEvents;
use App\WebSocket\WebSocketParser;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Socket\Dispatcher;
use EasySwoole\Utility\File;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        self::loadConf();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
        $swooleServer = ServerManager::getInstance()->getSwooleServer();
        $swooleServer->addProcess((new HotReload('HotReload',['disableInotify' => false]))->getProcess());

        //创建一个 Dispatcher 服务
        $conf = new \EasySwoole\Socket\Config();
        //设置dispatcher 为socket 模式
        $conf->setType($conf::WEB_SOCKET);
        //设置解析器对象
        $conf->setParser(new WebSocketParser());
        //创建dispatcher对象 并注入config对象
        $dispatcher = new Dispatcher($conf);
        //给server注册相关事件 在WebSocket模式下 message事件必须注册 并且交给Dispatcher对象处理
        $register->set(EventRegister::onMessage,function(\swoole_server $server,\swoole_websocket_frame $frame) use ($dispatcher){
            $dispatcher->dispatch($server,$frame->data,$frame);
        });
        //连接打开和关闭的处理
        $register->set(EventRegister::onOpen,[WebSocketEvents::class,'onOpen']);
//        $register->set(EventRegister::onClose,[WebSocketEvents::class,'onClose']);
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

    public static function loadConf(){
        $files = File::scanDirectory(EASYSWOOLE_ROOT . '/App/Conf');
        if (is_array($files)) {
            foreach ($files['files'] as $file) {
                $fileNameArr = explode('.', $file);
                $fileSuffix = end($fileNameArr);
                if ($fileSuffix == 'php') {
                    Config::getInstance()->loadFile($file);//引入之后,文件名自动转为小写,成为配置的key
                }
            }
        }
    }
}