<?php

namespace Tests\Editora;

use Mockery\MockInterface;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Tests\TestCase;

final class DeleteInstanceTest extends TestCase
{
    /** @test */
    public function deleteInstanceSuccessfully(): void
    {
        $instance = $this->mock(Instance::class);
        $this->mock(InstanceRepositoryInterface::class, function (MockInterface $mock) use ($instance) {
            $mock->shouldReceive('find')->once()->andReturn($instance);
            $mock->shouldReceive('delete')->once()->with($instance)->andReturn(null);
        });

        $this->deleteJson('123')
            ->assertStatus(204);
    }
}
