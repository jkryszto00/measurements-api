<?php

namespace App\Http\Controllers\API\Export;

use App\Http\Controllers\API\ApiController;
use App\Models\Measurement;
use App\Services\Export\DownloadExportService;
use App\Services\Export\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends ApiController
{
    public function __invoke(Request $request, ExportService $exportService, DownloadExportService $downloadExportService): StreamedResponse|JsonResponse
    {
        try {
            $headers = ['Data', 'Waga', 'Netto', 'Brutto', 'Produkt', 'Numery pojazdu', 'Klient', 'Kierowca'];
            $data = Measurement::filter($request->all())->orderBy('id', 'desc')->select('created_at', 'unit', 'netto', 'brutto', 'product', 'plate', 'customer', 'driver')->get()->toArray();

            $spreadsheet = $exportService->export($headers, $data);

            return $downloadExportService->download($spreadsheet);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
