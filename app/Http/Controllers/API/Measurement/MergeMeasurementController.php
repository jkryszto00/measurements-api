<?php

namespace App\Http\Controllers\API\Measurement;

use App\Exceptions\Measurement\MeasurementHaveNettoValueException;
use App\Http\Controllers\API\ApiController;
use App\Http\Resources\MeasurementResource;
use App\Models\Measurement;
use App\Services\Measurement\MergeMeasurementService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class MergeMeasurementController extends ApiController
{
    public function __invoke(Request $request, MergeMeasurementService $mergeMeasurementService): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                '0' => 'required|integer|exists:measurements,id',
                '1' => 'required|integer|exists:measurements,id'
            ]);

            $measurements = [
                Measurement::with('attachments')->findOrFail($validated[0]),
                Measurement::with('attachments')->findOrFail($validated[1])
            ];

            $mergedMeasurement = $mergeMeasurementService->merge($measurements);

            return $this->handleResponse('Measurements merged successfully', new MeasurementResource($mergedMeasurement), Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('One of measurements could not be found.', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
                return $this->handleError($e->getMessage(), $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (MeasurementHaveNettoValueException $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
