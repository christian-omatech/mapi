<?php

namespace Tests\Editora\Controllers\Mock;

use Mockery\MockInterface;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Tests\TestCase;

final class ReadInstanceTest extends TestCase
{
    /** @test */
    public function readInstanceSuccessfully(): void
    {
        $instance = $this->mock(Instance::class, function (MockInterface $mock) {
            $mock->shouldReceive('toArray')->once();
        });
        $this->mock(InstanceRepositoryInterface::class, function (MockInterface $mock) use ($instance) {
            $mock->shouldReceive('find')->once()->andReturn($instance);
        });

        $response = $this->getJson('123');
        $response->assertStatus(200);
    }
}
