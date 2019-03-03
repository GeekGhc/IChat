<?php
namespace App\Utility\Pool;


use EasySwoole\Component\Pool\PoolObjectInterface;

class RedisPoolObject extends \Redis implements PoolObjectInterface
{
    function gc(){
        $this->close();
    }

    function objectRestore()
    {
        // TODO: Implement objectRestore() method.
    }

    function object($string = '', $key = '')
    {
        parent::object($string, $key); // TODO: Change the autogenerated stub
    }

    function beforeUse(): bool
    {
        // TODO: Implement beforeUse() method.
        return true;
    }
}