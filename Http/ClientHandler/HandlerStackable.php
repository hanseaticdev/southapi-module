<?php

namespace Modules\SouthAPI\Http\ClientHandler;

interface HandlerStackable
{
    public function getStack(): \GuzzleHttp\HandlerStack;
}
