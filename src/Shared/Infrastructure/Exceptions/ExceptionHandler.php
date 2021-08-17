<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Exceptions;

use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceExistsException;

final class ExceptionHandler extends Handler
{
    public function register()
    {
        if (request()->wantsJson()) {
            $this->renderable(function (ValidationException $exception) {
                return new JsonResponse([
                    'status' => $exception->status,
                    'error' => $exception->errors(),
                    'message' => $exception->getMessage(),
                ], $exception->status);
            });
        }

        $this->renderable(function (InstanceExistsException $exception) {
            return new JsonResponse([
                'status' => 422,
                'error' => '',
                'message' => $exception->getMessage(),
            ], 422);
        });
    }
}
