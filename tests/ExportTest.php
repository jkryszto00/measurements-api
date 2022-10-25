<?php

use App\Services\Export\ExportService;

class ExportTest extends TestCase
{
    public function testExportMeasurements()
    {
        $exportService = new ExportService();
        $headers = ['Data', 'Waga', 'Netto', 'Brutto', 'Produkt', 'Numery pojazdu', 'Klient', 'Kierowca'];
        $measurements = \App\Models\Measurement::factory()->count(5)->create();

        $result = $exportService->export($headers, $measurements->toArray());
        $this->assertIsObject($result);
    }

    public function testPrepareDataForExport()
    {
        $exportService = $this->app->make(ExportService::class);
        $exportServiceReflection = new ReflectionClass($exportService);

        $canMergeMeasurements = $exportServiceReflection->getMethod('prepareData');
        $canMergeMeasurements->setAccessible('public');

        $headers = ['Data', 'Waga', 'Netto', 'Brutto', 'Produkt', 'Numery pojazdu', 'Klient', 'Kierowca'];
        $measurements = \App\Models\Measurement::factory()->count(5)->create();
        $result = $canMergeMeasurements->invoke($exportService, $headers, $measurements->toArray());

        $this->assertIsArray($result);
    }
}
