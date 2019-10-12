<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\DomainEvent;

interface EventHandler
{
    public function handle(DomainEvent $event): void;
    /** @return string[] */
    public function events(): iterable;
}
