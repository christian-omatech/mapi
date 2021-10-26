<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Loaders;

use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts\StructureCacheInterface;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts\StructureLoaderInterface;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Loaders\Exceptions\ClassNotFoundException;
use Symfony\Component\Yaml\Yaml;

class YamlStructureLoader implements StructureLoaderInterface
{
    private StructureCacheInterface $structureCache;

    public function __construct(StructureCacheInterface $structureCache)
    {
        $this->structureCache = $structureCache;
    }

    public function load(string $classKey): array
    {
        $structure = $this->structureCache->get();
        if (!$structure) {
            $structure = Yaml::parseFile(config('mage.editora.structure_path'));
            $this->structureCache->put($structure);
        }
        return $structure['classes'][$classKey] ?? ClassNotFoundException::withClass($classKey);
    }
}
