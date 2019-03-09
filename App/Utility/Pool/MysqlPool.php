<?php
namespace App\Utility\Pool;


use EasySwoole\Component\Pool\AbstractPool;
use EasySwoole\Mysqli\Config;

class MysqlPool extends AbstractPool
{
    /**
     *
     */
    protected function createObject()
    {
        // TODO: Implement createObject() method.
        //连接池第一次获取连接时 会调用此方法
        $conf = \EasySwoole\EasySwoole\Config::getInstance()->getConf('mysql');
        $dbConf = new Config($conf);
        return new MysqlPoolObject($dbConf);

    }
}