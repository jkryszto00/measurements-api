<?php

namespace App\Http\Controllers\API\Measurement;

use App\Exceptions\Autosuggestion\AutosuggestionQueryValueIsEmpty;
use App\Exceptions\Autosuggestion\AutosuggestionTypeValueIsEmpty;
use App\Exceptions\Autosuggestion\AutosuggestionTypeValueIsWrong;
use App\Http\Controllers\API\ApiController;
use App\Services\Measurement\AutosuggestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AutosuggestionMeasurementController extends ApiController
{
    public function __invoke(Request $request, AutosuggestionService $autosuggestionService): JsonResponse
    {
        try {
            $type = $request->get('type');
            $query = $request->get('query');

            if (!$type) {
                throw new AutosuggestionTypeValueIsEmpty('Type value cannot be empty');
            }

            if (!$query) {
                throw new AutosuggestionQueryValueIsEmpty('Query value cannot be empty');
            }

            $suggestions = $autosuggestionService->suggestions($type, $query);

            return $this->handleWithDataResponse($suggestions, Response::HTTP_OK);
        } catch(AutosuggestionTypeValueIsEmpty|AutosuggestionQueryValueIsEmpty|AutosuggestionTypeValueIsWrong $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch(\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
