<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CommandHandling\Handler;

final class CardPlayingProcess implements Handler
{
    public function handle(object $command): void
    {
        assert($command instanceof PlayTheCard);


    }
}
