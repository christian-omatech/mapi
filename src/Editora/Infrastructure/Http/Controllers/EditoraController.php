<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\ControllerDispatcher;
use Omatech\Mapi\Shared\Infrastructure\Http\Controllers\Controller;
use Omatech\Mcore\Editora\Application\ExtractionLocator\ExtractionLocatorCommand;

final class EditoraController extends Controller
{
    protected array $uris;
    private Controller $controller;

    public function __construct()
    {
        parent::__construct();
        if (! app()->runningInConsole()) {
            $extractorLocator = $this->queryBus->handle(new ExtractionLocatorCommand([
                'languages' => config('mage.editora.languages'),
                'router' => config('mage.editora.router'),
                'niceUrl' => request()->route('niceUrl'),
                'uri' => request()->route()->uri(),
                'path' => request()->path(),
            ]));
            $this->controller = app()->make($extractorLocator[0]);
            $this->uris = $extractorLocator[1];
            $this->middleware($this->controller->middlewares());
        }
    }

    public function __invoke(Request $request)
    {
        return (new ControllerDispatcher(app()))
            ->dispatch($request->route(), $this->controller, '__invoke');
    }
}
