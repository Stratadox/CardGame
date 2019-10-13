<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Command\BlockTheAttacker;
use Stratadox\CardGame\CommandHandler;
use Stratadox\CardGame\Match\Event\TriedBlockingOutOfTurn;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Match\NoSuchCard;
use Stratadox\CardGame\Match\NotYourTurn;
use Stratadox\Clock\Clock;

final class BlockingProcess implements CommandHandler
{
    /** @var Matches */
    private $matches;
    /** @var Clock */
    private $clock;
    /** @var EventBag */
    private $eventBag;

    public function __construct(
        Matches $matches,
        Clock $clock,
        EventBag $eventBag
    ) {
        $this->matches = $matches;
        $this->clock = $clock;
        $this->eventBag = $eventBag;
    }

    public function handle(Command $command): void
    {
        assert($command instanceof BlockTheAttacker);

        $this->sendIntoBattle(
            $this->matches->withId($command->match()),
            $command->player(),
            $command->defender(),
            $command->attacker(),
            $command->correlationId()
        );
    }

    private function sendIntoBattle(
        Match $theMatch,
        int $thePlayer,
        int $defender,
        int $attacker,
        CorrelationId $correlationId
    ): void {
        try {
            $theMatch->defendAgainst(
                $attacker,
                $defender,
                $thePlayer,
                $this->clock->now()
            );
        } catch (NoSuchCard $ohNo) {
            //@todo this happened: tried to defend with unknown card
        } catch (NotYourTurn $ohNo) {
            $this->eventBag->add(new TriedBlockingOutOfTurn(
                $correlationId,
                $ohNo->getMessage()
            ));
            return;
        }

        $this->eventBag->takeFrom($theMatch);
    }
}
