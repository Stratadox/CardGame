<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\CommandHandler;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Match\NotEnoughMana;
use Stratadox\CardGame\Match\NotYourTurn;
use Stratadox\CardGame\Match\Event\PlayerDidNotHaveTheMana;
use Stratadox\CardGame\Match\Command\PlayTheCard;
use Stratadox\CardGame\Match\Event\TriedPlayingCardOutOfTurn;
use Stratadox\Clock\Clock;

final class CardPlayingProcess implements CommandHandler
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

    public function handle(Command $command): void
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
        Match $match,
        int $player,
        int $cardNumber,
        CorrelationId $correlationId
    ): void {
        try {
            $match->playTheCard($cardNumber, $player, $this->clock->now());
        } catch (NotEnoughMana $problem) {
            $this->eventBag->add(new PlayerDidNotHaveTheMana(
                $correlationId,
                $problem->getMessage()
            ));
        } catch (NotYourTurn $problem) {
            $this->eventBag->add(new TriedPlayingCardOutOfTurn(
                $correlationId,
                $problem->getMessage()
            ));
        }

        $this->eventBag->takeFrom($match);
    }
}
