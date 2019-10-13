<?php declare(strict_types=1);

namespace Stratadox\CardGame;

interface Command
{
    public function correlationId(): CorrelationId;
}
