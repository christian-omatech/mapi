<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\ExtractionRepositoryInterface;
use Omatech\Mcore\Shared\Utils\Utils;

final class ExtractionRepository extends InstanceRepository implements ExtractionRepositoryInterface
{
    public function instanceByKey(array $params): array
    {
        $key = Utils::getInstance()->slug($params['key']);
        $instances = $this->instance->where('key', $key)
            ->get()
            ->map(
                fn ($instance) => $this
                    ->build($instance->class_key)
                    ->fill($this->instanceFromDB($instance))
            )->toArray();
        return [
            'pagination' => [],
            'instances' => $instances,
        ];
    }

    public function instancesByClass(array $params): array
    {
        $class = Utils::getInstance()->slug($params['class']);
        $total = $this->instance->where('class_key', $class)->count();
        $pagination = new Pagination($total, $params['limit'], $params['page']);
        $instances = $this->instance->where('class_key', $class)
            ->limit($pagination->realLimit())->offset($pagination->offset())
            ->get()->map(
                fn ($instance) => $this
                    ->build($instance->class_key)
                    ->fill($this->instanceFromDB($instance))
            )->toArray();
        return [
            'pagination' => $pagination->toArray(),
            'instances' => $instances,
        ];
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
