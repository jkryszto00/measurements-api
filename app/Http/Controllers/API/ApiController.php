<?php

namespace App\Http\Controllers\API;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends BaseController
{
    public function handleWithMessageResponse(string $message, int $code): JsonResponse
    {
        return response()->json([
            'message' => $message
        ], $code);
    }

    public function handleWithDataResponse(mixed $data, int $code): JsonResponse
    {
        return response()->json([
            'data' => $data
        ], $code);
    }

    public function handleResponse(string $message, mixed $data, int $code): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function handleErrorWithMessage(string $error, int $code): JsonResponse
    {
        return response()->json([
            'error' => $error
        ], $code);
    }

    public function handleError(string $error, mixed $errors, int $code): JsonResponse
    {
        return response()->json([
            'error' => $error,
            'errors' => $errors,
        ], $code);
    }
}
