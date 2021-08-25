<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Exceptions;

use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Loaders\Exceptions\ClassNotFoundException;
use Omatech\Mcore\Editora\Domain\Instance\Exceptions\InstanceExistsException;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\RequiredValueException;
use Omatech\Mcore\Editora\Domain\Instance\Validator\Exceptions\UniqueValueException;

final class ExceptionHandler extends Handler
{
    public function register()
    {
        $this->validationException();
        $this->instanceExistsException();
        $this->uniqueValueException();
        $this->classNotFoundException();
    }

    private function validationException(): void
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
    }

    private function instanceExistsException(): void
    {
        if (request()->wantsJson()) {
            $this->renderable(function (InstanceExistsException $exception) {
                return new JsonResponse([
                    'status' => 422,
                    'error' => '',
                    'message' => $exception->getMessage(),
                ], 422);
            });
        }
    }

    private function uniqueValueException(): void
    {
        if (request()->wantsJson()) {
            $this->renderable(function (UniqueValueException $exception) {
                return new JsonResponse([
                    'status' => 422,
                    'error' => '',
                    'message' => $exception->getMessage(),
                ], 422);
            });
        }
    }

    private function classNotFoundException(): void
    {
        if (request()->wantsJson()) {
            $this->renderable(function (ClassNotFoundException $exception) {
                return new JsonResponse([
                    'status' => 422,
                    'error' => '',
                    'message' => $exception->getMessage(),
                ], 422);
            });
        }
    }
}
