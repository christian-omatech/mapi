<?php declare(strict_types=1);

namespace Tests\Editora\Controllers\Mock;

use Mockery\MockInterface;
use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceRepositoryInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Tests\TestCase;

final class CreateInstanceTest extends TestCase
{
    /** @test */
    public function failedValidationOnCreateInstance(): void
    {
        $response = $this->postJson('/', []);
        $response->assertJson([
            'status' => '422',
            'error' => '',
            'message' => 'Class  not found.',
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function createInstanceSuccessfully(): void
    {
        $instance = $this->mock(Instance::class, function (MockInterface $mock) {
            $mock->shouldReceive('fill')->once()->andReturn($mock);
        });
        $this->mock(InstanceRepositoryInterface::class, function (MockInterface $mock) use ($instance) {
            $mock->shouldReceive('exists')->once()->andReturn(false);
            $mock->shouldReceive('build')->once()->andReturn($instance);
            $mock->shouldReceive('save')->once()->with($instance)->andReturn(null);
        });

        $response = $this->postJson('/', [
            'classKey' => 'ClassOne',
            'key' => 'test',
            'status' => 'pending',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [
                'all-languages-attribute' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'test',
                        ], [
                            'language' => 'en',
                            'value' => 'test',
                        ],
                    ],
                ],
            ],
        ]);
        $response->assertStatus(204);
    }

    /** @test */
    public function invalidHeadersOnPostCall(): void
    {
        $response = $this->post('/', [
            'classKey' => 'ClassOne',
            'key' => 'test',
            'status' => 'pending',
            'startPublishingDate' => '1989-03-08 09:00:00',
            'attributes' => [
                'all-languages-attribute' => [
                    'values' => [
                        [
                            'language' => 'es',
                            'value' => 'test',
                        ], [
                            'language' => 'en',
                            'value' => 'test',
                        ],
                    ],
                ],
            ],
        ]);
        $response->assertJson([
            'status' => '422',
            'error' => '',
            'message' => 'Class  not found.',
        ]);
        $response->assertStatus(422);
    }
}
