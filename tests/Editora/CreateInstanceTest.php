<?php

namespace Tests\Editora;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Repositories\InstanceRepository;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Tests\TestCase;

final class CreateInstanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function createInstance(): void
    {
        $instance = $this->mock(Instance::class, function (MockInterface $mock) {
            $mock->shouldReceive('fill')->with([
                'metadata' => [
                    'key' => 'test',
                    'publication' => [
                        'startPublishingDate' => '1989-03-08 09:00:00',
                        'endPublishingDate' => null
                    ]
                ],
                'attributes' => [],
                'relations' => []
            ])->once()->andReturn(null);
        });
        $this->mock(InstanceRepositoryInterface::class, function (MockInterface $mock) use ($instance) {
            $mock->shouldReceive('build')->once()->andReturn($instance);
            $mock->shouldReceive('save')->once()->with($instance)->andReturn(null);
        });

        $response = $this->postJson('/', [
            'class_key' => 'test',
            'metadata' => [
                'key' => 'test',
                'publication' => [
                    'start_publishing_date' => '1989-03-08 09:00:00'
                ]
            ],
            'attributes' => []
        ]);

        $response->assertStatus(204);
    }

    /** @test */
    public function createInstanceInMysql(): void
    {
        $this->app->bind(InstanceRepositoryInterface::class, InstanceRepository::class);

        $response = $this->postJson('/', [
            'class_key' => 'test',
            'metadata' => [
                'key' => 'test',
                'publication' => [
                    'start_publishing_date' => '1989-03-08 09:00:00'
                ]
            ],
            'attributes' => []
        ]);

        $this->assertDatabaseHas('mage_instances', [
            'uuid' => null,
            'class_key' => 'test',
            'key' => 'test',
            'status' => 'pending',
            'start_publishing_date' => '1989-03-08 09:00:00',
            'end_publishing_date' => null,
            'created_at' => null,
            'updated_at' => null,
            'deleted_at' => null,
        ]);

        $response->assertStatus(204);
    }
}
