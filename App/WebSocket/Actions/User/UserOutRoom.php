<?php
namespace App\WebSocket\Actions\User;


use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class UserOutRoom extends ActionPayload
{
    protected $action = WebSocketAction::USER_OUT_ROOM;
    protected $userFd;

    /**
     * @return mixed
     */
    public function getUserFd()
    {
        return $this->userFd;
    }

    /**
     * @param mixed $userFd
     */
    public function setUserFd($userFd): void
    {
        $this->userFd = $userFd;
    }
}