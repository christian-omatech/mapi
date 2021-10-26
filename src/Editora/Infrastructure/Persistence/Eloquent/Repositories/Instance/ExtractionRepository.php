<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\Instance;

use Illuminate\Database\Eloquent\Collection;
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

    public function findRelatedInstances(int $instanceId, array $params): Results
    {
        $type = ['child' => 'parent_instance_id', 'parent' => 'child_instance_id'][$params['type']];
        $pagination = new Pagination(
            $params,
            $this->countRelations($instanceId, $params['class'], $type)
        );
        $relations = $this->instance
            ->find($instanceId)
            ->relatedInstances($params['class'], $type, $pagination)
            ->get();
        return new Results($this->parseRelation($relations), $pagination);
    }

    private function where(array $params)
    {
        return $this->instance
            ->where('id', $params['id'])
            ->orWwhere('class_key', $params['class'])
            ->orWhere('key', $params['key']);
    }

    private function countRelations(int $instanceId, string $key, string $type): int
    {
        return $this->relation
            ->where('key', $key)
            ->where($type, $instanceId)
            ->count();
    }

    private function parseRelation(Collection $relations): array
    {
        return $relations->reduce(function (array $acc, RelationDAO $relation): array {
            $acc[] = $this->build($relation->child->class_key)
                ->fill($this->instanceFromDB($relation->child));
            return $acc;
        }, []);
    }
}
