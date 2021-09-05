<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Tactician;

final class QueryBus extends Bus
{
    public function __construct()
    {
        parent::__construct('query_middleware');
    }
}
