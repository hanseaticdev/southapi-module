<?php

namespace Modules\SouthAPI\Http\ClientHandler\Middleware;

use Closure;

abstract class Middleware
{
    abstract public function getMiddleware(): Closure;
}
