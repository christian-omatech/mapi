<?php

namespace Tests\Editora\Controllers\Mock;

use Mockery\MockInterface;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Tests\TestCase;

final class UpdateInstanceTest extends TestCase
{
    /** @test */
    public function updateInstanceSuccessfully(): void
    {
        $instance = $this->mock(Instance::class, function (MockInterface $mock) {
            $mock->shouldReceive('fill')->once()->andReturn($mock);
        });
        $this->mock(InstanceRepositoryInterface::class, function (MockInterface $mock) use ($instance) {
            $mock->shouldReceive('find')->once()->andReturn($instance);
            $mock->shouldReceive('save')->once()->with($instance)->andReturn(null);
        });

        $response = $this->putJson('123', [
            'key' => 'test',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'status' => 'pending',
            'attributes' => [
                'all-languages-attribute' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'test'
                        ], [
                            'language' => 'en',
                            'value' => 'test'
                        ]
                    ]
                ]
            ]
        ]);
        $response->assertStatus(204);
    }
}
