<?php

namespace App\Services\Export;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadExportService
{
    public function download(Spreadsheet $spreadsheet): StreamedResponse
    {
        $filename = $this->createFilename();

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

    protected function createFilename(): string
    {
        $nowDate = new Carbon();
        return "measurements-{$nowDate}.xlsx";
    }
}
