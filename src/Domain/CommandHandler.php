<?php declare(strict_types=1);

namespace Stratadox\CardGame;

interface CommandHandler
{
    public function handle(Command $command): void;
}
