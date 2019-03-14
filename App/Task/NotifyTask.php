<?php
namespace App\Task;

use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

class NotifyTask extends AbstractAsyncTask
{
    function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        return true;
    }

    function finish($result, $task_id)
    {
//        echo "finished task id = ".$task_id;
        // TODO: Implement finish() method.
    }
}