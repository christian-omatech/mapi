<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\ExtractionRepositoryInterface;
use Omatech\Mcore\Shared\Utils\Utils;

final class ExtractionRepository extends InstanceRepository implements ExtractionRepositoryInterface
{
    public function instanceByKey(array $params): array
    {
        $key = Utils::getInstance()->slug($params['filter']);
        return $this->instance->where('key', $key)
            ->get()
            ->map(
                fn ($instance) => $this
                    ->build($instance->class_key)
                    ->fill($this->instanceFromDB($instance))
            )->toArray();
    }

    public function instancesByClass(array $params): array
    {
    }

    public function findChildrenInstances(int $instanceId, string $key, array $params): array
    {
        $instances = $this->instance->where('id', $instanceId)
            ->with('relations', function ($q) use ($key, $params) {
                $q->where('key', $key)
                    ->limit($params['limit'])
                    ->with('child');
            })
            ->get();

        return $instances->reduce(function ($acc, $instance) {
            return $acc + $instance->relations->reduce(function ($acc, $relation) {
                $acc[] = $this->build($relation->child->class_key)
                    ->fill($this->instanceFromDB($relation->child));
                return $acc;
            }, []);
        }, []);
    }
}
