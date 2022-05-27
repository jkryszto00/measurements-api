<?php

namespace App\Repositories;

use App\Interfaces\MeasurementInterface;
use App\Models\Measurement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MeasurementRepository implements MeasurementInterface
{
    public function all()
    {
        // TODO: Implement all() method.
    }

    public function allWithPagination(int $perPage, array $filters)
    {
        $measurements = Measurement::with('attachments', 'modifiedBy')->orderBy('id', 'desc');

        if (isset($filters['from']) and !empty($filters['from'])) {
            $measurements->whereDate('date', '>=', new Carbon($filters['from']));
        }

        if (isset($filters['to']) and !empty($filters['to'])) {
            $measurements->whereDate('date', '<=', new Carbon($filters['to']));
        }

        if (isset($filters['unit']) and !empty($filters['unit'])) {
             $measurements->where('unit', 'like', '%'.$filters['unit'].'%');
        }
        
        if (isset($filters['product']) and !empty($filters['product'])) {
            $measurements->where('product', 'like', '%'.$filters['product'].'%');
        }

        if (isset($filters['plate']) and !empty($filters['plate'])) {
            $measurements->where('plate', 'like', '%'.$filters['plate'].'%');
        }

        if (isset($filters['customer']) and !empty($filters['customer'])) {
            $measurements->where('customer', 'like', '%'.$filters['customer'].'%');
        }

        if (isset($filters['driver']) and !empty($filters['driver'])) {
            $measurements->where('driver', 'like', '%'.$filters['driver'].'%');
        }

        if (isset($filters['trashed']) and !empty($filters['trashed'])) {
            if ($filters['trashed'] == 'with') {
                $measurements->withTrashed();
            } elseif ($filters['trashed'] == 'only') {
                $measurements->onlyTrashed();
            }
        }

        return $measurements->paginate($perPage)->through(function ($measurement) {
            return [
                'id' => $measurement->id,
                'date' => $measurement->date,
                'unit' => $measurement->unit,
                'netto' => $measurement->netto,
                'brutto' => $measurement->brutto,
                'product' => $measurement->product,
                'plate' => $measurement->plate,
                'customer' => $measurement->customer,
                'driver' => $measurement->driver,
                'modified_by' => ($measurement->modifiedBy) ? [
                    'id' => $measurement->modifiedBy->id,
                    'email' => $measurement->modifiedBy->email
                ] : null,
                'attachments' => $measurement->attachments->map(function ($attachment) {
                    return [
                        'url' => $attachment->url
                    ];
                }),
                'notes' => $measurement->notes,
                'updated_at' => $measurement->updated_at,
                'created_at' => $measurement->created_at,
                'deleted_at' => $measurement->deleted_at
            ];
        });
    }

    public function get(int $id)
    {
        // TODO: Implement get() method.
    }

    public function store(array $data)
    {
        $data['date'] = Carbon::now();
        $data['modified_by_id'] = Auth::id();

        $measurement = Measurement::create($data);

        return [
            'id' => $measurement->id,
            'date' => $measurement->date,
            'unit' => $measurement->unit,
            'netto' => $measurement->netto,
            'brutto' => $measurement->brutto,
            'product' => $measurement->product,
            'plate' => $measurement->plate,
            'customer' => $measurement->customer,
            'driver' => $measurement->driver,
            'notes' => $measurement->notes,
            'modified_by' => [
                'id' => $measurement->modifiedBy->id,
                'email' => $measurement->modifiedBy->email
            ],
            'updated_at' => $measurement->updated_at,
            'created_at' => $measurement->created_at,
            'deleted_at' => $measurement->deleted_at
        ];
    }

    public function update(int $id, array $data)
    {
        $measurement = Measurement::findOrFail($id);

        $data['modified_by_id'] = Auth::id();

        $measurement->update($data);

        return [
            'id' => $measurement->id,
            'date' => $measurement->date,
            'unit' => $measurement->unit,
            'netto' => $measurement->netto,
            'brutto' => $measurement->brutto,
            'product' => $measurement->product,
            'plate' => $measurement->plate,
            'customer' => $measurement->customer,
            'driver' => $measurement->driver,
            'notes' => $measurement->notes,
            'modified_by' => [
                'id' => $measurement->modifiedBy->id,
                'email' => $measurement->modifiedBy->email
            ],
            'attachments' => $measurement->attachments->map(function ($attachment) {
                return [
                    'url' => $attachment->url
                ];
            }),
            'updated_at' => $measurement->updated_at,
            'created_at' => $measurement->created_at,
            'deleted_at' => $measurement->deleted_at
        ];
    }

    public function delete(int $id)
    {
        return Measurement::destroy($id);
    }

    public function detection()
    {
        $firstMeasurementIdOnBackend = Measurement::orderBy('id', 'desc')->first();

        return $firstMeasurementIdOnBackend->id;
    }

    public function autosuggestion(array $data)
    {
        $key = $data['key'];
        $value = $data['value'];

        return Measurement::select($key)->where($key, 'LIKE', '%'. $value. '%')->distinct()->pluck($key);
    }
}
