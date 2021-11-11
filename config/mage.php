<?php declare(strict_types=1);

use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Loaders\YamlStructureLoader;
use Omatech\Mapi\Shared\Infrastructure\Tactician\HandlerLocator;
use Omatech\Mapi\Shared\Infrastructure\Tactician\Middleware\EloquentTransactionMiddleware;

return [

    /**
     * Editora
     */
    'editora' => [
        'languages' => ['es', 'en'],
        'structure_path' => storage_path('structure.yml'),
        'structure_loader' => YamlStructureLoader::class,
        'router' => [
            [
                'controller_namespace' => 'Omatech\FrontEnd\Infrastructure\Http\Controllers',
                'segments' => ['{language}', '{niceUrl}', 'products', '{uuid}'],
                'classes' => ['products'],
                'translate' => false
            ], [
                'controller_namespace' => 'Omatech\FrontEnd\Infrastructure\Http\Controllers',
                'segments' => ['{language}', '{niceUrl}', 'news', '{uuid}'],
                'classes' => ['products'],
                'translate' => false
            ],
        ]
    ],

    /**
     * Command bus
     */
    'commandbus' => [
        'command_name_extractor' => ClassNameExtractor::class,
        'handler_locator' => HandlerLocator::class,
        'method_name_inflector' => InvokeInflector::class,
        'command_middleware' => [
            EloquentTransactionMiddleware::class,
        ],
        'query_middleware' => [
        ],
    ]
];
