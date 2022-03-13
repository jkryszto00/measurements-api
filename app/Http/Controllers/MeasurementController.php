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
        $measurements = Measurement::whereIn('id', $request->input('measurements'))->with('attachments')->get();

        if (
            count($measurements) == 2
            and empty($measurements[0]->netto)
            and empty($measurements[1]->netto)
            and !empty($measurements[0]->brutto)
            and !empty($measurements[1]->brutto)
        ) {
            if ($measurements[0]->brutto == $measurements[1]->brutto) {
                $higher = $measurements[0];
                $lower = $measurements[1];
            } else {
                $higher = ($measurements[0]->brutto > $measurements[1]->brutto) ? $measurements[0] : $measurements[1];
                $lower = ($measurements[1]->brutto > $measurements[0]->brutto) ? $measurements[0] : $measurements[1];
            }

            foreach ($lower->attachments as $attachment) {
                $attachment->update(['measurement_id' => $higher->id]);
            }

            $measurement = $this->measurementInterface->update($higher->id, [
                'netto' => $higher->brutto - $lower->brutto,
                'product' => $this->mergeValues($higher->product, $lower->product),
                'plate' => $this->mergeValues($higher->plate, $lower->plate),
                'customer' => $this->mergeValues($higher->customer, $lower->customer),
                'driver' => $this->mergeValues($higher->driver, $lower->driver)
            ]);

            $this->measurementInterface->delete($lower->id);

            return response()->json([
                'status' => '201',
                'message' => 'Scalowanie pomiarów przebiegło pomyślnie',
                'measurement' => $measurement
            ], 201);
        }

        return response()->json([
            'status' => '401',
            'error' => 'Bad request'
        ]);
    }

    protected function mergeValues($value1, $value2)
    {
        if ($value1 and empty($value2)) {
            return $value1;
        } else if (empty($value1) and $value2) {
            return $value2;
        } else if ($value1 == $value2) {
            return $value1;
        } else {
            return $value1.', '.$value2;
        }
    }

    public function detection()
    {
        return response()->json([
           'status' => '200',
           'newest_id' => $this->measurementInterface->detection()
        ]);
    }
}
