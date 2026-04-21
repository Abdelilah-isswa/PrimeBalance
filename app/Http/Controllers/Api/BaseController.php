<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Success JSON response
     */
    protected function sendResponse($data, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Error JSON response
     */
    protected function sendError($error, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $error,
        ], $code);
    }

    /**
     * Created JSON response
     */
    protected function sendCreated($data, string $message = 'Created'): JsonResponse
    {
        return $this->sendResponse($data, $message, 201);
    }
}
