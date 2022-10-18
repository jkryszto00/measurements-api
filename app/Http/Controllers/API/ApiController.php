<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    public function handleWithMessageResponse(string $message, int $code): JsonResponse
    {
        return response()->json([
            'message' => $message
        ], $code);
    }

    public function handleWithDataResponse(array $data, int $code): JsonResponse
    {
        return response()->json([
            'data' => $data
        ], $code);
    }

    public function handleResponse(string $message, $data, int $code): JsonResponse
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

    public function handleError(string $error, $errors, int $code): JsonResponse
    {
        return response()->json([
            'errors' => $errors,
            'error' => $error,
        ], $code);
    }
}
