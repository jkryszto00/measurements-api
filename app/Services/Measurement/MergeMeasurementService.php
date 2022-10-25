<?php

namespace App\Services\Measurement;

use App\Exceptions\Measurement\MeasurementHaveNettoValueException;
use App\Models\Measurement;

class MergeMeasurementService
{
    public function __construct(
        private MeasurementService $measurementService
    ){}

    public function merge(array $measurements): Measurement
    {
        if (!$this->canMergeMeasurements($measurements)) {
            throw new MeasurementHaveNettoValueException('One of measurements have netto value');
        }

        $higher = $this->setHigherMeasurement($measurements);
        $lower = $this->setLowerMeasurement($measurements);

        $data = $this->mergeMeasurementsData($higher, $lower);

        $this->mergeAllAttachments($higher, $lower->attachments);
        $this->measurementService->updateMeasurement($higher, $data);
        $this->measurementService->deleteMeasurement($lower);

        return $higher;
    }

    private function canMergeMeasurements(array $measurements): bool
    {
        return $measurements[0]->netto == 0 and $measurements[1]->netto == 0;
    }

    private function setHigherMeasurement(array $measurements): Measurement
    {
        if ($measurements[0]->brutto >= $measurements[1]->brutto) {
            return  $measurements[0];
        }

        return $measurements[1];
    }

    private function setLowerMeasurement(array $measurements): Measurement
    {
        if ($measurements[0]->brutto >= $measurements[1]->brutto) {
            return $measurements[1];
        }

        return $measurements[0];
    }

    private function mergeMeasurementsData(Measurement $higher, Measurement $lower): array
    {
        return [
            'netto' => $higher->brutto - $lower->brutto,
            'unit' => $this->mergeValues($higher->unit, $lower->unit),
            'product' => $this->mergeValues($higher->product, $lower->product),
            'customer' => $this->mergeValues($higher->customer, $lower->customer),
            'plate' => $this->mergeValues($higher->plate, $lower->plate),
            'driver' => $this->mergeValues($higher->driver, $lower->driver),
            'notes' => $this->mergeValues($higher->notes, $lower->notes)
        ];
    }

    private function mergeValues($higherValue, $lowerValue): string
    {
        if (strcmp($higherValue, $lowerValue) == 0) {
            return $higherValue;
        } else {
            return $higherValue.', '.$lowerValue;
        }
    }

    private function mergeAllAttachments($measurement, $attachments): void
    {
        foreach ($attachments as $attachment) {
            $newAttachment = $attachment->replicate();
            $newAttachment->measurement_id = $measurement->id;
            $newAttachment->save();
        }
    }
}
