<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Vhost;

interface VhostRepositoryInterface
{
    public function getList(int $userId): LengthAwarePaginator;
    public function get(array $data): ?Vhost;
    public function create(array $data): Vhost;
    public function delete(Vhost $vhost): bool;
    public function getAvailablePort(): int;
}
