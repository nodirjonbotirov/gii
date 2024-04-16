<?php

namespace Botiroff\Gii\Classes;

use Exception;

class ServiceException extends Exception
{
    public function report(): array
    {
        return [
            'line' => $this->getLine(),
            'file' => $this->getFile(),
            'message' => $this->getMessage(),
        ];
    }
}
