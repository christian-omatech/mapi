<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Events;

use Omatech\Mcore\Shared\Domain\Event\Contracts\EventPublisherInterface;
use Omatech\Mcore\Shared\Domain\Event\Event;
use function Lambdish\Phunctional\each;

class EventPublisher implements EventPublisherInterface
{
    public function publish(array ...$events): void
    {
        each(fn (Event $event) => event($event), $events);
    }
}
