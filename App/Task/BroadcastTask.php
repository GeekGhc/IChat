<?php
/**
 * Created by PhpStorm.
 * User: gehuachun
 * Date: 2019-02-19
 * Time: 18:20
 */
namespace App\Task;

use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

Class BroadcastTask extends AbstractAsyncTask{

    /**
     * @param $taskData
     * @param $taskId
     * @param $fromWorkerId
     * @param null $flags
     * @return bool
     */
    protected function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        return true;
    }
    function finish($result, $task_id)
    {
        // TODO: Implement finish() method.
    }
}