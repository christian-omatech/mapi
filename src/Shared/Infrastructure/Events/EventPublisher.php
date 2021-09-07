<?php declare(strict_types=1);

namespace Omatech\Mapi\Shared\Infrastructure\Events;

use Omatech\Mcore\Shared\Domain\Event\Contracts\EventPublisherInterface;
use Omatech\Mcore\Shared\Domain\Event\Event;
use function Lambdish\Phunctional\each;

final class EventPublisher implements EventPublisherInterface
{
    /** @param array<Event> $events */
    public function publish(array $events): void
    {
        each(fn (Event $event) => event($event), $events);
    }
}
