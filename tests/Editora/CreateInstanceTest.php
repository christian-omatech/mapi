<?php

namespace Tests\Editora;

use Mockery\MockInterface;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Tests\TestCase;

final class CreateInstanceTest extends TestCase
{
    /** @test */
    public function createInstance(): void
    {
        $instance = $this->mock(Instance::class, function(MockInterface $mock) {
            $mock->shouldReceive('fill')->once()->andReturn(null);
        });
        $this->mock(InstanceRepositoryInterface::class, function(MockInterface $mock) use ($instance) {
            $mock->shouldReceive('build')->once()->andReturn($instance);
            $mock->shouldReceive('save')->once()->with($instance)->andReturn(null);
        });

        $response = $this->post('/');
        $response->assertStatus(204);
    }
}
