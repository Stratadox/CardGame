<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test;

use Stratadox\CardGame\Infrastructure\DomainEvents\Dispatcher;
use Stratadox\CommandHandling\Handler;

interface Configuration
{
    public function handler(Dispatcher $dispatcher): Handler;
}
