<?php
namespace App\WebSocket\Actions\User;


use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class UserInRoom extends ActionPayload
{
    protected $action = WebSocketAction::USER_IN_ROOM;
    protected $info;

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param $info
     */
    public function setInfo($info):void
    {
        $this->info = $info;
    }
}