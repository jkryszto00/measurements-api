<?php

namespace App\Http\Controllers\API\Measurement;

use App\Http\Controllers\API\ApiController;
use App\Http\Resources\MeasurementResource;
use App\Models\Measurement;
use App\Services\Measurement\MeasurementService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class MeasurementController extends ApiController
{
    public function __construct(
        private MeasurementService $measurementService
    ){}

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = ($request->get('per_page') and is_integer((int) $request->get('per_page'))) ? $request->get('per_page') : 50;

            $measurements = Measurement::filter($request->all())->with('attachments')->orderBy('id', 'desc')->paginate($perPage);
            return $this->handleWithDataResponse(MeasurementResource::collection($measurements), Response::HTTP_OK);
        } catch(\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $measurement = Measurement::findOrFail($id);
            return $this->handleWithDataResponse(new MeasurementResource($measurement), Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('Measurement not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'unit' => 'required|string|max:255',
                'netto' => 'nullable|integer',
                'brutto' => 'nullable|integer',
                'product' => 'nullable|string|max:255',
                'customer' => 'nullable|string|max:255',
                'plate' => 'nullable|string|max:255',
                'driver' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:255',
                'attachments' => 'nullable|array',
                'attachments.*' => 'required|image'
            ]);

            $measurement = $this->measurementService->storeMeasurement($validated);

            return $this->handleResponse('Measurement created', new MeasurementResource($measurement), Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->handleError($e->getMessage(), $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        try {
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

            $measurement = Measurement::findOrFail($id);
            $this->measurementService->updateMeasurement($measurement, $validated);

            return $this->handleResponse('Measurement updated', new MeasurementResource($measurement), Response::HTTP_CREATED);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('Measurement not found', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return $this->handleError($e->getMessage(), $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $measurement = Measurement::findOrFail($id);
            $this->measurementService->deleteMeasurement($measurement);
            return $this->handleWithMessageResponse('Measurement deleted', Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('Measurement not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
