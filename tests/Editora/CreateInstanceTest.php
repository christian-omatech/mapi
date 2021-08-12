<?php

namespace Tests\Editora;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
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
            $mock->shouldReceive('fill')->once()->andReturn(null);
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
}
