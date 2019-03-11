<?php
namespace App\Task;

use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

class NotifyTask extends AbstractAsyncTask
{
    protected function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        // TODO: Implement run() method.
    }

    protected function finish($result, $task_id)
    {
        // TODO: Implement finish() method.
    }
}