<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Omatech\Mcore\Editora\Application\ReadInstance\ReadInstanceCommand;
use Omatech\Mapi\Shared\Infrastructure\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

final class ReadInstanceController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $instance = $this->queryBus->handle(new ReadInstanceCommand(1));
        return new JsonResponse($instance, Response::HTTP_OK);
    }
}
