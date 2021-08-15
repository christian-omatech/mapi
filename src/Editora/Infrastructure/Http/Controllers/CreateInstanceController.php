<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Omatech\Mapi\Editora\Infrastructure\Http\Requests\CreateInstanceRequest;
use Omatech\Mapi\Shared\Infrastructure\Http\Controllers\Controller;
use Omatech\Mcore\Editora\Application\CreateInstance\CreateInstanceCommand;
use Symfony\Component\HttpFoundation\Response;

final class CreateInstanceController extends Controller
{
    public function __invoke(CreateInstanceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->commandBus->handle(new CreateInstanceCommand([
            'classKey' => $data['class_key'],
            'metadata' => $data['metadata'],
            'attributes' => $data['attributes'] ?? [],
            'relations' => $data['relations'] ?? [],
        ]));
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
