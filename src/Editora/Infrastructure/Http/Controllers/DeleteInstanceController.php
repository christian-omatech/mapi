<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Omatech\Mapi\Editora\Infrastructure\Http\Requests\DeleteInstanceRequest;
use Omatech\Mapi\Shared\Infrastructure\Http\Controllers\Controller;
use Omatech\Mcore\Editora\Application\DeleteInstance\DeleteInstanceCommand;
use Symfony\Component\HttpFoundation\Response;

final class DeleteInstanceController extends Controller
{
    public function __invoke(DeleteInstanceRequest $request): JsonResponse
    {
        $this->commandBus->handle(new DeleteInstanceCommand($request->validated()['uuid']));
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
