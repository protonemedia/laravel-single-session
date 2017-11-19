<?php

namespace Pbmedia\SingleSession\Tests;

class FakeUser
{
    private $saved = false;

    public function save()
    {
        $this->saved = true;
    }

    public function saved()
    {
        return $this->saved;
    }
}
