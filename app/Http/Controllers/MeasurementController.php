<?php

namespace App\Http\Controllers;

use App\Http\Requests\MergeRequest;
use App\Interfaces\MeasurementInterface;
use App\Models\Measurement;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function __construct(MeasurementInterface $measurementInterface)
    {
        $this->measurementInterface = $measurementInterface;
    }

    public function index()
    {
        $perPage = (request()->input('perPage')) ? request()->perPage : 10;
        return response()->json([
            'status' => 200,
            'pagination' => $this->measurementInterface->allWithPagination($perPage, request()->only(
                'from', 'to', 'unit', 'product', 'plate', 'customer', 'driver', 'trashed'
            )),
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'status' => 201,
            'message' => 'Pomiar utworzono pomyślnie.',
            'measurement' => $this->measurementInterface->store($request->only(
                'unit', 'netto', 'brutto', 'product', 'plate', 'customer', 'driver', 'notes'
            ))
        ], 201);
    }

    public function update($id, Request $request)
    {
        return response()->json([
            'status' => 201,
            'message' => 'Zaktualizowano pomiar pomyślnie',
            'measurement' => $this->measurementInterface->update($id, $request->only(
                'unit', 'netto', 'brutto', 'product', 'plate', 'customer', 'driver', 'notes'
            ))
        ], 201);
    }

    public function delete($id)
    {
        $this->measurementInterface->delete($id);

        return response()->json([
            'status' => 204,
            'message' => 'Usunięto pomiar pomyślnie.'
        ]);
    }

    public function autosuggestion(Request $request)
    {
        return response()->json([
            'status' => 200,
            'suggestions' => $this->measurementInterface->autosuggestion($request->only('key', 'value'))
        ]);
    }

    public function merge(Request $request)
    {
        $measurements = Measurement::whereIn('id', $request->input('measurements'))->get();

        if (count($measurements) == 2 and empty($measurements[0]->netto) and empty($measurements[1]->netto)) {
            $higher = ($measurements[0]->brutto > $measurements[1]->brutto) ? $measurements[0] : $measurements[1];
            $lower = ($measurements[1]->brutto > $measurements[0]->brutto) ? $measurements[0] : $measurements[1];

            if ($this->measurementInterface->delete($lower->id)) {
                return response()->json([
                    'status' => 201,
                    'message' => 'Scalowanie pomiarów przebiegło pomyślnie',
                    'measurement' => $this->measurementInterface->update($higher->id, [
                        'netto' => ($higher->brutto - $lower->brutto),
                        'product' => $higher->product.', '.$lower->product,
                        'plate' => $higher->plate.', '.$lower->plate,
                        'customer' => $higher->customer.', '.$lower->customer,
                        'driver' => $higher->driver.', '.$lower->driver
                    ])
                ]);
            }
        }

        return response()->json([
            'status' => '401',
            'error' => 'Bad request'
        ]);
    }

    public function detection()
    {
        return response()->json([
           'status' => '200',
           'newest_id' => $this->measurementInterface->detection()
        ]);
    }
}
