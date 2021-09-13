<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance;

use Illuminate\Database\Eloquent\Collection;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\RelationDAO;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\ExtractionRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Pagination;
use Omatech\Mcore\Editora\Domain\Instance\Extraction\Results;

final class ExtractionRepository extends InstanceRepository implements ExtractionRepositoryInterface
{
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
        $pagination = new Pagination($params, $this->countRelations($instanceId, $params));
        $instances = $this->instance->where('id', $instanceId)
            ->with('relations', function ($q) use ($params, $pagination) {
                $q->where('key', $params['class'])
                    ->limit($pagination->realLimit())->offset($pagination->offset())
                    ->with('child');
            })
            ->get();
        return new Results($this->parseRelation($instances), $pagination);
    }

    private function where(array $params)
    {
        return $this->instance
            ->where('class_key', $params['class'])
            ->orWhere('key', $params['key']);
    }

    private function countRelations(int $instanceId, array $params): int
    {
        return $this->instance->where('id', $instanceId)
            ->withCount(['relations' => function ($q) use ($params) {
                $q->where('key', $params['class']);
            },
            ])->first()->relations_count;
    }

    private function parseRelation(Collection $instances): array
    {
        return $instances->reduce(function (array $acc, InstanceDAO $instance): array {
            return $acc + $instance->relations->reduce(
                function (array $acc, RelationDAO $relation): array {
                    $acc[] = $this->build($relation->child->class_key)
                        ->fill($this->instanceFromDB($relation->child));
                    return $acc;
                },
                []
            );
        }, []);
    }
}
