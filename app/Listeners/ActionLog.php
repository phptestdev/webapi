<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\VhostCreated;
use App\Events\VhostDeleted;
use App\Models\ActionLog as ActionLogModel;

class ActionLog
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(VhostCreated|VhostDeleted $event): void
    {
        $entity = $event->entity;

        ActionLogModel::create([
            'user_id' => $entity->user_id,
            'entity_id' => $entity->id,
            'entity' => $entity::class,
            'data' => $entity->toJson()
        ]);
    }
}
