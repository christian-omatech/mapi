<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use function DeepCopy\deep_copy;

final class InstanceRepository extends BaseRepository implements InstanceRepositoryInterface
{
    public function build(string $classKey): Instance
    {
        return $this->instanceBuilder->build($classKey);
    }

    public function clone(Instance $instance): Instance
    {
        return deep_copy($instance);
    }

    public function find(int $id): ?Instance
    {
        $model = $this->instance->find($id);
        return $this->build($model->class_key)
            ->fill($this->instanceFromDB($model));
    }

    public function findByKey(string $key): ?Instance
    {
        $model = $this->instance->where('key', $key)->first();
        return $this->build($model->class_key)
            ->fill($this->instanceFromDB($model));
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

    public function exists(string $key): bool
    {
        return $this->instance->where('key', $key)->exists();
    }

    public function classKey(int $id): ?string
    {
        return $this->instance->select(['class_key'])
            ->find($id)
            ?->class_key;
    }

    public function delete(Instance $instance): void
    {
        $this->instance->find($instance->id())->forceDelete();
    }

    public function save(Instance $instance): void
    {
        $this->instanceToDB($instance);
    }
}
