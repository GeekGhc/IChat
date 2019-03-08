<?php
namespace App\Utility\Pool;
use EasySwoole\Component\Pool\PoolObjectInterface;
use EasySwoole\Mysqli\Mysqli;

class MysqlPoolObject extends Mysqli implements PoolObjectInterface
{
    /**
     * 释放对象时使用
     */
    function gc(){
        //重置为初始状态
        $this->resetDbStatus();
        //关闭数据库连接
        $this->getMysqlClient()->close();
    }

    /**
     * 回收对象时会调用
     */
    function objectRestore()
    {
        // TODO: Implement objectRestore() method.
        //重置为初始状态
        $this->resetDbStatus();
    }

    /**
     * 每个链接使用之前 都会调用此方法 请返回 true / false
     * 返回false时PoolManager会回收该链接 并重新进入获取链接流程
     * @return bool
     */
    function beforeUse(): bool
    {
        // TODO: Implement beforeUse() method.
        // 此处可以进行链接是否断线的判断 使用不同的数据库操作类时可以根据自己情况修改
        return $this->getMysqlClient()->connected;
    }

}