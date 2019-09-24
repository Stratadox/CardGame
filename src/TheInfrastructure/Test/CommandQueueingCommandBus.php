<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CommandHandling\Handler;

final class CommandQueueingCommandBus implements Handler
{
    private $queue = [];

    public function handle(object $command): void
    {
        $this->queue[] = $command;
    }

    public function all(): array
    {
        return $this->queue;
    }

    public function clear(): void
    {
        $this->queue = [];
    }
}
