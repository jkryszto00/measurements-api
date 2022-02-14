<?php

namespace App\Http\Controllers;

use App\Interfaces\MeasurementInterface;
use App\Models\Measurement;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    public function __construct(MeasurementInterface $measurementInterface)
    {
        $this->measurementInterface = $measurementInterface;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $data = $this->prepareData(request()->only(
            'from', 'to', 'unit', 'product', 'plate', 'customer', 'driver', 'trashed'
        ));

        $spreadsheet->getActiveSheet()
            ->fromArray(
              $data,
              null,
                'A1'
            );

        $spreadsheet = $this->customSpreadsheet($spreadsheet);
        $filename = $this->createFilename();

        return $this->downloadRespone($spreadsheet, $filename);
    }

    protected function prepareData(array $filters) : array
    {
        $headers = ['Data', 'Waga', 'Netto', 'Brutto', 'Produkt', 'Numery pojazdu', 'Klient', 'Kierowca'];

        $data = Measurement::orderBy('id', 'desc')->select(
            'date', 'unit', 'netto', 'brutto', 'product', 'plate', 'customer', 'driver'
        );

        if (isset($filters['from']) and !empty($filters['from'])) {
            $data->whereDate('date', '>=', new Carbon($filters['from']));
        }

        if (isset($filters['to']) and !empty($filters['to'])) {
            $data->whereDate('date', '<=', new Carbon($filters['to']));
        }

        if (isset($filters['unit']) and !empty($filters['unit'])) {
            $data->where('unit', 'like', '%'.$filters['unit'].'%');
        }

        if (isset($filters['product']) and !empty($filters['product'])) {
            $data->where('product', 'like', '%'.$filters['product'].'%');
        }

        if (isset($filters['plate']) and !empty($filters['plate'])) {
            $data->where('plate', 'like', '%'.$filters['plate'].'%');
        }

        if (isset($filters['customer']) and !empty($filters['customer'])) {
            $data->where('customer', 'like', '%'.$filters['customer'].'%');
        }

        if (isset($filters['driver']) and !empty($filters['driver'])) {
            $data->where('driver', 'like', '%'.$filters['driver'].'%');
        }

        if (isset($filters['trashed']) and !empty($filters['trashed'])) {
            if ($filters['trashed'] == 'with') {
                $data->withTrashed();
            } elseif ($filters['trashed'] == 'only') {
                $data->onlyTrashed();
            }
        }

        $data = $data->get()->toArray();

        array_unshift($data, $headers);
        return $data;
    }

    protected function customSpreadsheet($spreadsheet) : object
    {
        foreach (range('A', 'H') as $l) {
            $spreadsheet->getActiveSheet()->getColumnDimension($l)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    protected function createFilename() : string
    {
        $nowDate = new Carbon();
        return "wazenia-{$nowDate}.xlsx";
    }

    protected function downloadRespone($spreadsheet, $filename)
    {
        $contentDisposition = "attachment; filename={$filename}";
        $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

        $response = response()->stream(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', $contentType);
        $response->headers->set('Content-Disposition', $contentDisposition);

        return $response;
    }
}
