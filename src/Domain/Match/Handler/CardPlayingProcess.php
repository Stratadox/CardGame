<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Match\NotEnoughMana;
use Stratadox\CardGame\Match\NotYourTurn;
use Stratadox\CardGame\Match\Event\PlayerDidNotHaveTheMana;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\Match\Event\TriedPlayingCardOutOfTurn;
use Stratadox\Clock\Clock;
use Stratadox\CommandHandling\Handler;

final class CardPlayingProcess implements Handler
{
    private $matches;
    private $clock;
    private $eventBag;

    public function __construct(Matches $matches, Clock $clock, EventBag $eventBag)
    {
        $this->matches = $matches;
        $this->clock = $clock;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof PlayTheCard);

        $this->play(
            $this->matches->withId($command->match()),
            $command->player(),
            $command->cardNumber(),
            $command->correlationId()
        );
    }

    private function play(
        Match $theMatch,
        int $player,
        int $cardNumber,
        CorrelationId $correlationId
    ): void {
        try {
            $theMatch->playTheCard($cardNumber, $player, $this->clock->now());
        } catch (NotEnoughMana $problem) {
            $this->eventBag->add(new PlayerDidNotHaveTheMana($correlationId));
        } catch (NotYourTurn $problem) {
            $this->eventBag->add(new TriedPlayingCardOutOfTurn($correlationId));
        }

        $this->eventBag->takeFrom($theMatch);
    }
}
