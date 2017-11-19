<?php

namespace Pbmedia\SingleSession\Tests;

class FakeDestroyEvent
{
    public $user;
    public $sessionId;

    public function __construct($user, $sessionId)
    {
        $this->user      = $user;
        $this->sessionId = $sessionId;
    }
}
