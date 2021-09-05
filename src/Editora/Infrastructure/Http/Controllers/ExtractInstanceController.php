<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Omatech\Mapi\Editora\Infrastructure\Http\Requests\ExtractInstanceRequest;
use Omatech\Mapi\Shared\Infrastructure\Http\Controllers\Controller;
use Omatech\Mcore\Editora\Application\ExtractInstance\ExtractInstanceCommand;
use Symfony\Component\HttpFoundation\Response;

final class ExtractInstanceController extends Controller
{
    public function __invoke(ExtractInstanceRequest $request): JsonResponse
    {
        $extractions = $this->queryBus->handle(
            new ExtractInstanceCommand($request->validated()['query'])
        );
        return new JsonResponse($extractions->toArray(), Response::HTTP_OK);
    }
}
