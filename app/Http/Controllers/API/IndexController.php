<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class IndexController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        return $this->handleWithMessageResponse('Measurements api', Response::HTTP_OK);
    }
}
