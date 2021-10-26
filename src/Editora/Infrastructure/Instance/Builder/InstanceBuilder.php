<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Instance\Builder;

use Illuminate\Support\Str;
use Omatech\Mapi\Editora\Infrastructure\Instance\Builder\Contracts\StructureLoaderInterface;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder as CoreInstanceBuilder;

final class InstanceBuilder
{
    private StructureLoaderInterface $structureLoader;
    private InstanceCacheInterface $instanceCache;

    public function __construct(
        StructureLoaderInterface $structureLoader,
        InstanceCacheInterface $instanceCache
    ) {
        $this->structureLoader = $structureLoader;
        $this->instanceCache = $instanceCache;
    }

    public function build(string $classKey): Instance
    {
        $classKey = Str::ucfirst(Str::camel($classKey));
        return (new CoreInstanceBuilder($this->instanceCache))
            ->setLanguages(config('mage.editora.languages'))
            ->setStructure($this->structureLoader->load($classKey))
            ->setClassName($classKey)
            ->build();
    }
}
