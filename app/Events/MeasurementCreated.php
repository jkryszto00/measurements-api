<?php

namespace App\Events;

use App\Http\Resources\MeasurementResource;
use App\Models\Measurement;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MeasurementCreated extends Event implements ShouldBroadcast
{
    public function __construct(
        private Measurement $measurement
    ){}

    public function broadcastWith()
    {
        return $this->measurement->toArray();
    }

    public function broadcastOn()
    {
        return 'measurements';
    }
}
