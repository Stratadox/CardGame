<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Command\AttackWithCard;
use Stratadox\CardGame\CommandHandler;
use Stratadox\CardGame\Match\Event\TriedAttackingOutOfTurn;
use Stratadox\CardGame\Match\Event\TriedAttackingWithUnknownCard;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Match\NoSuchCard;
use Stratadox\CardGame\Match\NotYourTurn;
use Stratadox\Clock\Clock;

final class AttackingProcess implements CommandHandler
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
        assert($command instanceof AttackWithCard);

        $this->sendIntoBattle(
            $this->matches->withId($command->match()),
            $command->player(),
            $command->cardNumber(),
            $command->correlationId()
        );
    }

    public function sendIntoBattle(
        Match $match,
        int $player,
        int $cardNumber,
        CorrelationId $correlationId
    ): void {
        try {
            $match->attackWithCard($cardNumber, $player, $this->clock->now());
        } catch (NoSuchCard $ohNo) {
            // @todo log?
            $this->eventBag->add(new TriedAttackingWithUnknownCard(
                $correlationId,
                'That card does not exist'
            ));
            return;
        } catch (NotYourTurn $ohNo) {
            $this->eventBag->add(new TriedAttackingOutOfTurn(
                $correlationId,
                $ohNo->getMessage()
            ));
            return;
        }
        $this->eventBag->takeFrom($match);
    }
}
