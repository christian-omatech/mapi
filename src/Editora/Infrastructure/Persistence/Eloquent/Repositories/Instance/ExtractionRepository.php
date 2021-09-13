<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\ExtractionRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Pagination;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Results;

final class ExtractionRepository extends InstanceRepository implements ExtractionRepositoryInterface
{
    private function where(array $params)
    {
        return $this->instance
            ->where('class_key', $params['class'])
            ->orWhere('key', $params['key']);
    }

    public function instancesBy(array $params): Results
    {
        $pagination = new Pagination($params, $this->where($params)->count());
        $instances = $this->where($params)
            ->limit($pagination->realLimit())->offset($pagination->offset())
            ->get()->map(fn ($instance) => $this->buildFill($instance))->toArray();
        return new Results($instances, $pagination);
    }

    public function findChildrenInstances(int $instanceId, array $params): Results
    {
        $pagination = new Pagination($params, $this->where($params)->count());
        $instances = $this->instance->where('id', $instanceId)
            ->with('relations', function ($q) use ($params, $pagination) {
                $q->where('key', $params['class'])
                    ->limit($pagination->realLimit())->offset($pagination->offset())
                    ->with('child');
            })
            ->get();
        $instances = $instances->reduce(function ($acc, $instance) {
            return $acc + $instance->relations->reduce(function ($acc, $relation) {
                $acc[] = $this->build($relation->child->class_key)
                    ->fill($this->instanceFromDB($relation->child));
                return $acc;
            }, []);
        }, []);
        return new Results($instances, $pagination);
    }
}
