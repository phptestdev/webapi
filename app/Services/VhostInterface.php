<?php

namespace App\Services;

use App\Models\Vhost;

interface VhostInterface
{
    public function get(array $data): Vhost;
    public function create(array $data): Vhost;
    public function delete(): bool;
}
