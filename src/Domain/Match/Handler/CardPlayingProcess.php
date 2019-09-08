<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CommandHandling\Handler;

final class CardPlayingProcess implements Handler
{
    private $matches;
    private $eventBag;

    public function __construct(Matches $matches, EventBag $eventBag)
    {
        $this->matches = $matches;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof PlayTheCard);

        $match = $this->matches->playedBy($command->player());

        $match->playCard($command->offset(), $command->player());

        $this->eventBag->takeFrom($match);
    }
}
