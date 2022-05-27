<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MeasurementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'unit' => $this->unit,
            'netto' => $this->netto ?? 0,
            'brutto' => $this->brutto ?? 0,
            'product' => $this->product ?? '',
            'customer' => $this->customer ?? '',
            'plate' => $this->plate ?? '',
            'driver' => $this->driver ?? '',
            'modified_by' => new UserResource($this->modifiedBy),
//            'images' => ImageResource::collection($this->images),
            'notes' => $this->notes ?? '',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
