<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Omatech\Mapi\Shared\Infrastructure\Http\Controllers\Controller;
use Omatech\Mcore\Editora\Application\DeleteInstance\DeleteInstanceCommand;
use Symfony\Component\HttpFoundation\Response;

final class DeleteInstanceController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $this->commandBus->handle(new DeleteInstanceCommand(1));
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
