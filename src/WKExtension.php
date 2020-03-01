<?php

namespace honray;

use think\App;

abstract class WKExtension
{
    protected $ctx;

    public function __construct(App $app)
    {
        $this->ctx = $app;
    }

    abstract function init();
}