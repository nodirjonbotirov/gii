<?php

namespace Botiroff\Gii\Classes;

class ActionResult
{
    public bool $success = true;

    public ?string $message;

    public ?int $code;

    public mixed $data;

    public function setError(string $message, int $code = 500, mixed $data = null): static
    {
        $this->success = false;
        $this->message = $message;
        $this->data = $data;
        $this->code = $code;
        return $this;
    }

    public function setSuccess(mixed $data, int $code = 200, string $message = 'Success!'): static
    {
        $this->success = true;
        $this->data = $data;
        $this->message = $message;
        $this->code = $code;
        return $this;
    }
}
