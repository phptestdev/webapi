<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\VhostCreated;
use App\Events\VhostDeleted;

class Vhost extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'domain',
        'port',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'created' => VhostCreated::class,
        'deleted' => VhostDeleted::class,
    ];
}
