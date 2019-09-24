<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Player;

use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Card\Card;
use Stratadox\CardGame\Match\Card\Cards;
use Stratadox\CommandHandling\Handler;
use function assert;

final class CardBuyingProcess implements Handler
{
    private $players;
    private $cards;
    private $eventBag;

    public function __construct(
        Players $players,
        Cards $cards,
        EventBag $eventBag
    ) {
        $this->players = $players;
        $this->cards = $cards;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof PlayTheCard);

        $this->payFor(
            $this->cards->inHandOf($command->player())[$command->offset()],
            $this->players->withId($command->player())
        );
    }

    private function payFor(Card $theCardToBuy, Player $thePlayer): void
    {
        try {
            $thePlayer->payFor($theCardToBuy->id(), $theCardToBuy->cost());
        } catch (CannotPlayThisCard $forSomeReason) {
            // @todo tell 'em
        }

        $this->eventBag->takeFrom($thePlayer);
    }
}
