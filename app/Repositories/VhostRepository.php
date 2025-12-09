<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Vhost;

class VhostRepository implements VhostRepositoryInterface
{
    public const ROWS_PER_PAGE = 20;

    protected Vhost $model;

    public function __construct(Vhost $model)
    {
        $this->model = $model;
    }

    /**
     * Gets virtual host list.
     *
     * @param int $userId
     * @return LengthAwarePaginator
     */
    public function getList(int $userId): LengthAwarePaginator
    {
        return $this->model->where('user_id', $userId)->paginate(self::ROWS_PER_PAGE);
    }

    /**
     * Gets virtual host entity by data.
     *
     * @param array $data
     * @return ?Vhost
     */
    public function get(array $data): ?Vhost
    {
        return $this->model->where($data)->first();
    }

    /**
     * Creates virtual host entity.
     *
     * @param array $data
     * @return Vhost
     */
    public function create(array $data): Vhost
    {
        return $this->model->create($data);
    }

    /**
     * Deletes virtual host entity.
     *
     * @param Vhost $vhost
     * @return bool
     */
    public function delete(Vhost $vhost): bool
    {
        return $vhost->delete();
    }

    /**
     * Gets available port.
     *
     * @return int
     */
    public function getAvailablePort(): int
    {
        $port = $this->model::max('port');

        return ($port < 8082) ? 8082 : ($port + 1);
    }
}
