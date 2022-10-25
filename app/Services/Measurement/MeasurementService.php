<?php

namespace App\Services\Measurement;

use App\Events\MeasurementCreated;
use App\Models\Measurement;

class MeasurementService
{
    public function storeMeasurement(array $attributes): Measurement
    {
        $measurement = Measurement::create($attributes);

        if (!empty($attributes['attachments'])) {
            foreach ($attributes['attachments'] as $attachment) {
                (new AttachmentService($measurement))->upload($attachment);
            }
        }

        event(new MeasurementCreated($measurement));

        return $measurement;
    }

    public function updateMeasurement(Measurement $measurement, array $attributes): Measurement
    {
        $measurement->update($attributes);

        return $measurement;
    }

    public function deleteMeasurement(Measurement $measurement): void
    {
        $measurement->delete();
    }
}
