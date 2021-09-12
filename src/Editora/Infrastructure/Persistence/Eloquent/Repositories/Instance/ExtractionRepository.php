<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\ExtractionRepositoryInterface;

final class ExtractionRepository extends InstanceRepository implements ExtractionRepositoryInterface
{
    private function where(array $params)
    {
        return $this->instance
            ->where('class_key', $params['class'])
            ->orWhere('key', $params['key']);
    }

    public function instancesBy(array $params): array
    {
        $pagination = new Pagination($params, $this->where($params)->count());
        $instances = $this->where($params)
            ->limit($pagination->realLimit())->offset($pagination->offset())
            ->get()->map(fn ($instance) => $this->buildFill($instance))->toArray();
        return [
            'pagination' => $pagination->toArray(),
            'instances' => $instances,
        ];
    }

    public function findChildrenInstances(int $instanceId, array $params): array
    {
        $instances = $this->instance->where('id', $instanceId)
            ->with('relations', function ($q) use ($params) {
                $q->where('key', $params['class'])
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
