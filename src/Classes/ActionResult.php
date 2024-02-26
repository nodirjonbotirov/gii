<?php

namespace Botiroff\Gii\Classes;

final class ActionResult
{
    public bool $success = true;

    public ?string $message;

    public ?int $code;

    public mixed $data;

    public static function error(string $message, int $code = 500, mixed $data = null): ActionResult
    {
        return (new ActionResult)($data, $code, $message, false);
    }

    public static function success(mixed $data, int $code = 200, string $message = 'Success!'): ActionResult
    {
        return (new ActionResult)($data, $code, $message);
    }

    public function __invoke(mixed $data, int $code, string $message, bool $success = true): ActionResult
    {
        $this->success = $success;
        $this->data = $data;
        $this->message = $message;
        $this->code = $code;
        return $this;
    }
}
