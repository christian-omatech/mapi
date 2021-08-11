<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Exceptions;

use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class ExceptionHandler extends Handler
{
    public function register()
    {
        $this->renderable(function (ValidationException $exception) {
            return new JsonResponse([
                'status' => $exception->status,
                'error' => $exception->errors(),
                'message' => $exception->getMessage(),
            ], $exception->status);
        });

        $this->renderable(function (AccessDeniedHttpException $exception) {
            return new JsonResponse([
                'status' => 403,
                'error' => '',
                'message' => $exception->getMessage(),
            ], 403);
        });
    }
}
