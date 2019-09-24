<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Card\PutIntoPlay;
use Stratadox\CardGame\Match\Player\PaidForCard;
use Stratadox\CommandHandling\Handler;

final class CardPlayingSaga implements EventHandler
{
    private $nextStep;

    public function __construct(Handler $nextStep)
    {
        $this->nextStep = $nextStep;
    }

    public function handle(DomainEvent $event): void
    {
        if ($event instanceof PaidForCard) {
            $this->playCard($event);
        }
    }

    private function playCard(PaidForCard $paid): void
    {
        $this->nextStep->handle(PutIntoPlay::the($paid->card()));
    }
}
