<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Omatech\Mapi\Editora\Infrastructure\Http\Requests\ReadInstanceRequest;
use Omatech\Mapi\Shared\Infrastructure\Http\Controllers\Controller;
use Omatech\Mcore\Editora\Application\ReadInstance\ReadInstanceCommand;
use Symfony\Component\HttpFoundation\Response;

final class ReadInstanceController extends Controller
{
    public function __invoke(ReadInstanceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $instance = $this->queryBus->handle(new ReadInstanceCommand($data['id']));
        return new JsonResponse($instance, Response::HTTP_OK);
    }
}
