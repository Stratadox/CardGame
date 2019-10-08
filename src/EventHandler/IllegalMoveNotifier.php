<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\PlayerDidNotHaveTheMana;
use Stratadox\CardGame\Match\TriedPlayingCardOutOfTurn;
use Stratadox\CardGame\ReadModel\IllegalMoveStream;

final class IllegalMoveNotifier implements EventHandler
{
    private $illegalMoves;

    public function __construct(IllegalMoveStream $illegalMoves)
    {
        $this->illegalMoves = $illegalMoves;
    }

    public function handle(DomainEvent $event): void
    {
        if ($event instanceof PlayerDidNotHaveTheMana) {
            $this->illegalMoves->addFor($event->match(), $event->player(), 'Not enough mana!');
        }
        if ($event instanceof TriedPlayingCardOutOfTurn) {
            $this->illegalMoves->addFor($event->match(), $event->player(), 'Cannot play cards right now.');
        }
    }
}
