<?php declare(strict_types=1);

namespace Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories;

use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder;

final class InstanceRepository implements InstanceRepositoryInterface
{
    private function model()
    {
        return InstanceDAO::query();
    }

    public function build(string $classKey): Instance
    {
        return (new InstanceBuilder())
            ->setLanguages(['es', 'en'])
            ->setStructure([
                'attributes' => [
                    'AllLanguagesAttribute' => [],
                ],
            ])
            ->setClassName('test')
            ->build();
    }
    public function find(int $id): ?Instance
    {
    }
    public function classKey(int $id): ?string
    {
    }
    public function delete(Instance $instance): void
    {
    }

    public function save(Instance $instance): void
    {
        $data = $instance->toArray();
        $this->model()->insert([
            'class_key' => $data['class']['key'],
            'key' => $data['metadata']['key'],
            'status' => $data['metadata']['publication']['status'],
            'start_publishing_date' => $data['metadata']['publication']['startPublishingDate'],
        ]);
    }
}
