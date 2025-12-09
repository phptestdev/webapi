<?php

namespace App\Services;

interface WebserverInterface
{
    public function start(): bool;
    public function stop(): bool;
    public function restart(): bool;
    public function reload(): bool;
}
