<?php

namespace App\Http\Controllers\API;

use App\Exceptions\MeasurementHaveNettoValueException;
use App\Http\Resources\MeasurementResource;
use App\Models\Measurement;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeasurementController extends BaseController
{
    public function all(): JsonResponse
    {
        $perPage = (request()->get('perPage')) ? request()->get('perPage') : 25;
        $measurements = Measurement::latest('id')->load('attachments');

        if (request()->get('from') and !empty(request()->get('from'))) {
            $measurements->whereDate('created_at', '>=', new Carbon(request()->get('from')));
        }

        if (request()->get('to') and !empty(request()->get('to'))) {
            $measurements->whereDate('created_at', '<=', new Carbon(request()->get('to')));
        }

        if (request()->get('unit') and !empty(request()->get('unit'))) {
            $measurements->where('unit', 'like', '%'.request()->get('unit').'%');
        }

        if (request()->get('product') and !empty(request()->get('product'))) {
            $measurements->where('product', 'like', '%'.request()->get('product').'%');
        }

        if (request()->get('customer') and !empty(request()->get('customer'))) {
            $measurements->where('customer', 'like', '%'.request()->get('customer').'%');
        }

        if (request()->get('plate') and !empty(request()->get('plate'))) {
            $measurements->where('plate', 'like', '%'.request()->get('plate').'%');
        }

        if (request()->get('driver') and !empty(request()->get('driver'))) {
            $measurements->where('driver', 'like', '%'.request()->get('driver').'%');
        }

        if (request()->get('status') and !empty(request()->get('status'))) {
            if (request()->get('status') == 'all') {
                $measurements->withTrashed();
            } else if (request()->get('status') == 'trashed') {
                $measurements->onlyTrashed();
            }
        }

        $pagination = $measurements->paginate($perPage);

        return $this->handleResponse('', [
            'measurements' => MeasurementResource::collection($pagination),
            'pagination' => [
                'current_page' => $pagination->currentPage(),
                'per_page' => $pagination->perPage(),
                'last_page' => $pagination->lastPage()
            ]
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validate($request, [
            'unit' => 'required|string|max:255',
            'netto' => 'nullable|integer',
            'brutto' => 'nullable|integer',
            'product' => 'nullable|string|max:255',
            'customer' => 'nullable|string|max:255',
            'plate' => 'nullable|string|max:255',
            'custom' => 'nullable|string|max:255',
            'driver' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255'
        ]);

        $measurement = Measurement::create($validated);
        return $this->handleResponse('Pomiar utworzono pomyślnie!', new MeasurementResource($measurement), 201);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $validated = $this->validate($request, [
            'unit' => 'required|string|max:255',
            'netto' => 'nullable|integer',
            'brutto' => 'nullable|integer',
            'product' => 'nullable|string|max:255',
            'customer' => 'nullable|string|max:255',
            'plate' => 'nullable|string|max:255',
            'driver' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255'
        ]);

        try {
            $measurement = Measurement::findOrFail($id);
            $measurement->update($validated);
            return $this->handleResponse('Pomiar zaktualizowano pomyślnie!', new MeasurementResource($measurement), 201);
        } catch (ModelNotFoundException $e) {
            return $this->handleError('Brak takiego pomiaru', [], 404);
        } catch (\Exception $e) {
            return $this->handleError('Wystąpił błąd spróbuj ponownie później', [], 400);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $measurement = Measurement::findOrFail($id);
            $measurement->delete();

            return $this->handleResponse('Usunięto pomiar pomyślnie!', [], 200);
        } catch (ModelNotFoundException $e) {
            return $this->handleError('Brak takiego pomiaru', [], 404);
        } catch (\Exception $e) {
            return $this->handleError('Wystąpił błąd spróbuj ponownie później', [], 400);
        }

    }

    public function merge(Request $request): JsonResponse
    {
        try {
            $firstMeasurement = Measurement::findOrFail($request->first_measurement);
            $secondMeasurement = Measurement::findOrFail($request->second_measurement);

            if ($firstMeasurement->netto or $secondMeasurement->netto) throw new MeasurementHaveNettoValueException('Jeden z pomiarów posiada wartość netto');

            if ($firstMeasurement->brutto > $secondMeasurement->brutto) {
                $higher = $firstMeasurement;
                $lower = $secondMeasurement;
            } else {
                $higher = $secondMeasurement;
                $lower = $firstMeasurement;
            }

            $data = [
                'netto' => $higher->brutto - $lower->brutto,
                'unit' => (strcmp($higher->unit, $lower->unit) == 0 ? $higher->unit : $higher->unit.', '.$lower->unit),
                'product' => (strcmp($higher->product, $lower->product) == 0 ? $higher->product : $higher->product.', '.$lower->product),
                'customer' => (strcmp($higher->customer, $lower->customer) == 0 ? $higher->customer : $higher->customer.', '.$lower->customer),
                'plate' => (strcmp($higher->plate, $lower->plate) == 0 ? $higher->plate : $higher->plate.', '.$lower->plate),
                'driver' => (strcmp($higher->driver, $lower->driver) == 0 ? $higher->driver : $higher->driver.', '.$lower->driver),
                'notes' => (strcmp($higher->notes, $lower->notes) == 0 ? $higher->notes : $higher->notes.', '.$lower->notes),
            ];

            foreach ($lower->attachments as $attachment) {
                $newAttachment = $attachment->replicate();
                $newAttachment->measurement_id = $higher->id;
                $newAttachment->save();
            }

            $higher->update($data);
            $lower->delete();

            return $this->handleResponse('Scalono pomiary pomyślnie!', [new MeasurementResource($higher)], 201);
        } catch (ModelNotFoundException $e) {
            return $this->handleError('Jeden z pomiarów nie istnieje', [], 404);
        } catch (MeasurementHaveNettoValueException $e) {
            return $this->handleError($e->getMessage(), [], 400);
        } catch (\Exception $e) {
            return $this->handleError('Wystąpił błąd spróbuj ponownie później', [], 400);
        }
    }

    public function autosuggestion(): JsonResponse
    {
        $type = request()->get('type');
        $query = request()->get('query');

        return $this->handleResponse('', [
            'type' => $type,
            'query' => $query,
            'suggestions' => Measurement::withTrashed()->where('netto', 0)->where($type, 'like', '%'.$query.'%')->distinct()->pluck($type)
        ], 200);
    }

    public function detect(): JsonResponse
    {
        return $this->handleResponse('', [
            'last_id' => Measurement::latest('id')->pluck('id')->first(),
        ], 200);
    }

    public function export()
    {

    }
}
