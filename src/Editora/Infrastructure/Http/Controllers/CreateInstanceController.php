<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Omatech\Mapi\Editora\Infrastructure\Http\Requests\CreateInstanceRequest;
use Omatech\Mapi\Shared\Infrastructure\Http\Controllers\Controller;
use Omatech\Mcore\Editora\Application\CreateInstance\CreateInstanceCommand;
use Symfony\Component\HttpFoundation\Response;

final class CreateInstanceController extends Controller
{
    public function __invoke(CreateInstanceRequest $request): JsonResponse
    {
        $this->commandBus->handle(new CreateInstanceCommand($request->validated()));
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
