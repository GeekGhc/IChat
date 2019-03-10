<?php
namespace App\WebSocket\Actions\Broadcast;

use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class BroadcastAdmin extends ActionPayload
{
    protected $action = WebSocketAction::BROADCAST_ADMIN;
    protected $content;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }
}