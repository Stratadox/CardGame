<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Command\EndTheTurn;
use Stratadox\CardGame\CommandHandler;
use Stratadox\CardGame\Match\Event\TriedEndingAlreadyEndedTurn;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Match\NotYourTurn;
use Stratadox\Clock\Clock;

final class TurnEndingProcess implements CommandHandler
{
    /** @var Matches */
    private $matches;
    /** @var Clock */
    private $clock;
    /** @var EventBag */
    private $eventBag;

    public function __construct(Matches $matches, Clock $clock, EventBag $eventBag)
    {
        $this->matches = $matches;
        $this->clock = $clock;
        $this->eventBag = $eventBag;
    }

    public function handle(Command $command): void
    {
        assert($command instanceof EndTheTurn);

        $this->endTurn(
            $this->matches->withId($command->match()),
            $command->player(),
            $command->correlationId()
        );
    }

    private function endTurn(Match $match, int $player, CorrelationId $id): void
    {
        try {
            $match->endTurnOf($player, $this->clock->now());
        } catch (NotYourTurn $cannotEndAnything) {
            $this->eventBag->add(
                new TriedEndingAlreadyEndedTurn(
                    $id,
                    $cannotEndAnything->getMessage()
                )
            );
            return;
        }
        $this->eventBag->takeFrom($match);
    }
}
