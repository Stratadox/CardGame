<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use function array_shift;
use Stratadox\CommandHandling\Handler;

final class OneAtATimeBus implements Handler
{
    // @todo Add to handler package
    private $remainingCommands = [];
    private $isHandling = false;
    private $handler;

    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    public function handle(object $command): void
    {
        $this->remainingCommands[] = $command;

        if (!$this->isHandling) {
            $this->isHandling = true;

            while ($command = array_shift($this->remainingCommands)) {
                $this->handler->handle($command);
            }

            $this->isHandling = false;
        }
    }
}
