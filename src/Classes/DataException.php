<?php

namespace Botiroff\Gii\Classes;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataException extends Exception
{
    use JsonResponseTrait;

    public function render(Request $request): JsonResponse
    {
        return $this->errorResponse([
            'message' => $this->getMessage(),
            'trace' => $this->getTrace()[0],
        ]);
    }
}
