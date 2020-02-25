<?php

namespace honray;

class FunctionBuilder
{
    public function build($path): WKFunctions
    {
        return new WKFunctions($path);
    }
}