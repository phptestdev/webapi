<?php

namespace App\Repositories;

use App\Models\AvailablePort;

interface AvailablePortRepositoryInterface
{
    public function getEntity(): ?AvailablePort;
    public function create(array $data): AvailablePort;
    public function delete(?AvailablePort $availablePort): bool;
}
