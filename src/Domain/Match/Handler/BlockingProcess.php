<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Command\BlockTheAttacker;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Match\NoSuchCard;
use Stratadox\CardGame\Match\NotYourTurn;
use Stratadox\Clock\Clock;
use Stratadox\CommandHandling\Handler;

final class BlockingProcess implements Handler
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

    public function handle(object $command): void
    {
        assert($command instanceof BlockTheAttacker);

        $this->sendIntoBattle(
            $this->matches->withId($command->match()),
            $command->player(),
            $command->defender(),
            $command->attacker()
        );
    }

    private function sendIntoBattle(
        Match $theMatch,
        int $thePlayer,
        int $defender,
        int $attacker
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
            //@todo this happened: tried to defend out-of-turn
        }

        $this->eventBag->takeFrom($theMatch);
    }
}
