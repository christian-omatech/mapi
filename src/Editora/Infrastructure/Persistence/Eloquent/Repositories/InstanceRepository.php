<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories;

use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance\BaseRepository;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;

final class InstanceRepository extends BaseRepository implements InstanceRepositoryInterface
{
    public function build(string $classKey): Instance
    {
        return $this->instanceBuilder->build($classKey);
    }

    public function find(int $id): ?Instance
    {
        $model = $this->instance->find($id);
        return $this->build($model->class_key)
            ->fill($this->instanceFromDB($model));
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
