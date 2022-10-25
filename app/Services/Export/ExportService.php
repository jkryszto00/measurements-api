<?php

namespace App\Services\Export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExportService
{
    public function export(array $headers, array $data): Spreadsheet
    {
        $preparedData = $this->prepareData($headers, $data);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($preparedData);

        foreach (range('A', 'H') as $l) {
            $spreadsheet->getActiveSheet()->getColumnDimension($l)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    private function prepareData(array $headers, array $data): array
    {
        array_unshift($data, $headers);
        return $data;
    }
}
