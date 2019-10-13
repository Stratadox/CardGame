<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\DomainEvents;

use function get_class;
use function is_array;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\EventHandler\EventHandler;

class Dispatcher
{
    /** @var EventHandler[][] */
    private $handlers;

    public function __construct(EventHandler ...$handlers)
    {
        foreach ($handlers as $eventHandler) {
            foreach ($eventHandler->events() as $event) {
                $this->registerHandler($event, $eventHandler);
            }
        }
    }

    public function dispatch(DomainEvent $event): void
    {
        foreach ($this->handlers[get_class($event)] ?? [] as $handler) {
            $handler->handle($event);
        }
    }

    public function registerHandler(string $event, EventHandler $handler): void
    {
        $this->handlers[$event][] = $handler;
    }
}
