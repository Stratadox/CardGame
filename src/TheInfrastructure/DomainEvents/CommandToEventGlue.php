<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\DomainEvents;

use Stratadox\CardGame\EventBag;
use Stratadox\CommandHandling\Middleware;

final class CommandToEventGlue implements Middleware
{
    private $eventBag;
    private $dispatcher;

    public function __construct(EventBag $eventBag, Dispatcher $dispatcher)
    {
        $this->eventBag = $eventBag;
        $this->dispatcher = $dispatcher;
    }

    public function invoke(object $command): void
    {
        foreach ($this->eventBag->all() as $domainEvent) {
            $this->dispatcher->dispatch($domainEvent);
        }
        $this->eventBag->clear();
    }
}
