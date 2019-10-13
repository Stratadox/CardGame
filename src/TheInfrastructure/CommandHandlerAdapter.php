<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure;

use function assert;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\CommandHandler;
use Stratadox\CommandHandling\Handler;

final class CommandHandlerAdapter implements Handler
{
    /** @var CommandHandler */
    private $handler;

    public function __construct(CommandHandler $handler)
    {
        $this->handler = $handler;
    }

    public function handle(object $command): void
    {
        assert($command instanceof Command);
        $this->handler->handle($command);
    }
}
