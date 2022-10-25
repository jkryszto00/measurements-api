<?php

use App\Models\Measurement;
use App\Services\Measurement\MeasurementService;
use App\Services\Measurement\MergeMeasurementService;
use Illuminate\Support\Str;

class MeasurementTest extends TestCase
{
    public function testCanCreateMeasurement()
    {
        $measurementService = new MeasurementService();
        $factoryMeasurement = Measurement::factory()->make();

        $createdMeasurement = $measurementService->storeMeasurement($factoryMeasurement->toArray());

        $this->seeInDatabase('measurements', ['id' => $createdMeasurement->id]);
    }

    public function testCanUpdateMeasurement()
    {
        $measurementService = new MeasurementService();
        $createdMeasurement = Measurement::factory()->create();
        $factoryMeasurement = Measurement::factory()->make();

        $measurementService->updateMeasurement($createdMeasurement, $factoryMeasurement->toArray());

        $this->seeInDatabase('measurements', ['id' => $createdMeasurement->id, 'unit' => $factoryMeasurement->unit]);
    }

    public function testCanDeleteMeasurement()
    {
        $measurementService = new MeasurementService();
        $factoryMeasurement = Measurement::factory()->create();

        $measurementService->deleteMeasurement($factoryMeasurement);

        $this->seeInDatabase('measurements', ['id' => $factoryMeasurement->id])->notSeeInDatabase('measurements', ['id' => $factoryMeasurement->id, 'deleted_at' => null]);
    }

    private function callPrivateMethod($method, ...$params)
    {
        $mergeMeasurementService = $this->app->make(MergeMeasurementService::class);
        $mergeMeasurementServiceReflection = new ReflectionClass($mergeMeasurementService);

        $function = $mergeMeasurementServiceReflection->getMethod($method);
        $function->setAccessible('public');

        return $function->invoke($mergeMeasurementService, ...$params);
    }

    public function testFirstMeasurementIsHigher()
    {
        $measurements = [
            new Measurement(['brutto' => 1]),
            new Measurement(['brutto' => 0]),
        ];

        $result = $this->callPrivateMethod('setHigherMeasurement', $measurements);

        $this->assertEquals($measurements[0], $result);
    }

    public function testSecondMeasurementIsHigher()
    {
        $measurements = [
            new Measurement(['brutto' => 0]),
            new Measurement(['brutto' => 1]),
        ];

        $result = $this->callPrivateMethod('setHigherMeasurement', $measurements);

        $this->assertEquals($measurements[1], $result);
    }

    public function testTwoMeasurementsAreEqualHigher()
    {
        $measurements = [
            new Measurement(['brutto' => 0]),
            new Measurement(['brutto' => 0]),
        ];

        $result = $this->callPrivateMethod('setHigherMeasurement', $measurements);

        $this->assertEquals($measurements[0], $result);
    }

    public function testFirstMeasurementIsLower()
    {
        $measurements = [
            new Measurement(['brutto' => 0]),
            new Measurement(['brutto' => 1]),
        ];

        $result = $this->callPrivateMethod('setLowerMeasurement', $measurements);

        $this->assertEquals($measurements[0], $result);
    }

    public function testSecondMeasurementIsLower()
    {
        $measurements = [
            new Measurement(['brutto' => 1]),
            new Measurement(['brutto' => 0]),
        ];

        $result = $this->callPrivateMethod('setLowerMeasurement', $measurements);

        $this->assertEquals($measurements[1], $result);
    }

    public function testTwoMeasurementsAreEqualLower()
    {
        $measurements = [
            new Measurement(['brutto' => 0]),
            new Measurement(['brutto' => 0]),
        ];

        $result = $this->callPrivateMethod('setLowerMeasurement', $measurements);

        $this->assertEquals($measurements[0], $result);
    }

    public function testValuesAreTheSame()
    {
        $firstValue = 'Test';
        $secondValue = 'Test';

        $result = $this->callPrivateMethod('mergeValues', $firstValue, $secondValue);

        $this->assertEquals('Test', $result);
    }

    public function testValuesAreDifferent()
    {
        $firstValue = Str::random(10);
        $secondValue = Str::random(10);

        $result = $this->callPrivateMethod('mergeValues', $firstValue, $secondValue);
        $expectedResult = $firstValue.', '.$secondValue;

        $this->assertEquals($expectedResult, $result);
    }

    public function testTwoMeasurmentsHaveZeroNettoValue()
    {
        $measurements = [
            new Measurement(['netto' => 0]),
            new Measurement(['netto' => 0]),
        ];

        $result = $this->callPrivateMethod('canMergeMeasurements', $measurements);
        $this->assertTrue($result);
    }

    public function testFirstMeasurementHaveZeroValue()
    {
        $measurements = [
            new Measurement(['netto' => 0]),
            new Measurement(['netto' => 1]),
        ];

        $result = $this->callPrivateMethod('canMergeMeasurements', $measurements);
        $this->assertFalse($result);
    }

    public function testSecondMeasurementHaveZeroValue()
    {
        $measurements = [
            new Measurement(['netto' => 1]),
            new Measurement(['netto' => 0]),
        ];

        $result = $this->callPrivateMethod('canMergeMeasurements', $measurements);
        $this->assertFalse($result);
    }

    public function testTwoMeasurementsHaveNotZeroValue()
    {
        $measurements = [
            new Measurement(['netto' => 1]),
            new Measurement(['netto' => 2]),
        ];

        $result = $this->callPrivateMethod('canMergeMeasurements', $measurements);
        $this->assertFalse($result);
    }
}
