<?php


namespace Botiroff\Gii\Classes;

use Illuminate\Http\JsonResponse;

trait JsonResponseTrait
{
    /**
     * Success method for controller
     * @param mixed $data
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse(mixed $data, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'error' => null,
            'data' => $data,
        ], $code, options: JSON_UNESCAPED_UNICODE);
    }

    /**
     * Error response for controller
     * @param mixed $message
     * @param int $code
     * @return JsonResponse
     */
    protected function errorResponse(mixed $message, int $code = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $message,
            'data' => null,
        ], $code, options: JSON_UNESCAPED_UNICODE);
    }
}
