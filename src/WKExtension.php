<?php

namespace honray;

use think\App;

abstract class WKExtension
{
    private $ctx;

    public function __construct(App $app)
    {
        $this->ctx = $this->app;
    }

    abstract function init();
}