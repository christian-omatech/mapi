<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Omatech\Mcore\Editora\Application\UpdateInstance\UpdateInstanceCommand;
use Omatech\Mapi\Shared\Infrastructure\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

final class UpdateInstanceController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $this->commandBus->handle(new UpdateInstanceCommand([
            'metadata' => [
                'id' => 1,
            ],
            'attributes' => [],
            'relations' => [],
        ]));
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
