<?php

namespace Pbmedia\SingleSession\Tests;

class FakeUser
{
    public $session_id = null;
    private $saved     = false;

    public function fresh()
    {
        $this->saved = false;
        return $this;
    }

    public function save()
    {
        $this->saved = true;
    }

    public function saved()
    {
        return $this->saved;
    }
}
