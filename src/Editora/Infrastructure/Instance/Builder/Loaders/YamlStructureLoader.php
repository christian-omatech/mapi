<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Loaders;

use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts\StructureLoaderInterface;
use Symfony\Component\Yaml\Yaml;

class YamlStructureLoader implements StructureLoaderInterface
{
    public function load(string $classKey): array
    {
        $yaml = Yaml::parseFile(config('mage.editora.structure_path'));
        return $yaml['classes'][$classKey];
    }
}
