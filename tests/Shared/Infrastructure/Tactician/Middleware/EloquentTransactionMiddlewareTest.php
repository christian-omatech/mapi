<?php

namespace Tests\Shared\Infrastructure\Tactician\Middleware;

use Exception;
use Omatech\Mapi\Editora\Infrastructure\Persistence\Eloquent\Models\InstanceDAO;
use Omatech\Mapi\Shared\Infrastructure\Tactician\Middleware\EloquentTransactionMiddleware;
use Tests\DatabaseTestCase;

class EloquentTransactionMiddlewareTest extends DatabaseTestCase
{
    /**
     * @test
     */
    public function eloquentTransactionFailed(): void
    {
        $this->expectException(Exception::class);
        $transaction = new EloquentTransactionMiddleware();
        $transaction->execute(new \stdClass(), function () {
            $model = new InstanceDAO();
            $model->class_key = 1;
            $model->key = 1;
            $model->status = 1;
            $model->start_publishing_date = '1989-03-08 09:00:00';
            $model->save();
            $model = new InstanceDAO();
            $model->class_key = 1;
            $model->key = 1;
            $model->status = 1;
            $model->save();
        });
    }
}
