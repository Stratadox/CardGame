<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Command\EndCardPlaying;
use Stratadox\CardGame\CommandHandler;
use Stratadox\CardGame\Match\Event\TriedEndingPlayPhaseOutOfTurn;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Match\NotYourTurn;
use Stratadox\Clock\Clock;

final class EndPlayPhaseProcess implements CommandHandler
{
    private $matches;
    private $eventBag;
    private $clock;

    public function __construct(
        Matches $matches,
        Clock $clock,
        EventBag $eventBag
    ) {
        $this->matches = $matches;
        $this->eventBag = $eventBag;
        $this->clock = $clock;
    }

    public function handle(Command $command): void
    {
        assert($command instanceof EndCardPlaying);

        $this->endPlayPhase(
            $command->player(),
            $this->matches->withId($command->match()),
            $command->correlationId()
        );
    }

    private function endPlayPhase(
        int $playerNumber,
        Match $match,
        CorrelationId $correlationId
    ): void {
        try {
            $match->endCardPlayingPhaseFor($playerNumber, $this->clock->now());
        } catch (NotYourTurn $refusal) {
            $this->eventBag->add(new TriedEndingPlayPhaseOutOfTurn(
                $correlationId,
                $refusal->getMessage()
            ));
            return;
        }

        $this->eventBag->takeFrom($match);
    }
}
