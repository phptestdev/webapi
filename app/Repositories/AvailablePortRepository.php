<?php

namespace App\Repositories;

use App\Models\AvailablePort;

class AvailablePortRepository implements AvailablePortRepositoryInterface
{
    protected AvailablePort $model;

    public function __construct(AvailablePort $model)
    {
        $this->model = $model;
    }

    /**
     * Gets available port entity.
     *
     * @return AvailablePort | NULL
     */
    public function getEntity(): ?AvailablePort
    {
        return $this->model::first();
    }

    /**
     * Creates available port entity.
     *
     * @param array $data
     * @return AvailablePort
     */
    public function create(array $data): AvailablePort
    {
        return $this->model->create($data);
    }

    /**
     * Deletes available port entity.
     *
     * @param AvailablePort | NULL $availablePort
     * @return bool
     */
    public function delete(?AvailablePort $availablePort): bool
    {
        if ($availablePort) {
            return $availablePort->delete();
        }

        return false;
    }
}
