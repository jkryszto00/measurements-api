<?php

namespace App\Services\Measurement;

use App\Models\Measurement;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class AttachmentService
{
    public function __construct(
        private Measurement $measurement
    ){}

    public function upload(File $file): bool
    {
        $name = $file->hashName();
        Storage::putFileAs('images', $file, $name);

        $this->attachToMeasurement($name);

        return true;
    }

    private function attachToMeasurement(string $path): void
    {
        $this->measurement->attachments()->create(['path' => $path]);
    }
}
