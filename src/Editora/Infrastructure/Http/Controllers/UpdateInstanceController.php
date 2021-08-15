<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Omatech\Mapi\Editora\Infrastructure\Http\Requests\UpdateInstanceRequest;
use Omatech\Mapi\Shared\Infrastructure\Http\Controllers\Controller;
use Omatech\Mcore\Editora\Application\UpdateInstance\UpdateInstanceCommand;
use Symfony\Component\HttpFoundation\Response;

final class UpdateInstanceController extends Controller
{
    public function __invoke(UpdateInstanceRequest $request): JsonResponse
    {
        $this->commandBus->handle(new UpdateInstanceCommand(
            $request->validated()
        ));
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
