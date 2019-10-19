<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function count;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Throwable;

final class Player implements DomainEventRecorder
{
    use DomainEventRecording;

    private $playerNumber;
    private $cards;
    private $maxHandSize;
    private $mana;

    public function __construct(
        int $id,
        Cards $cards,
        int $maxHandSize,
        Mana $mana
    ) {
        $this->playerNumber = $id;
        $this->cards = $cards;
        $this->maxHandSize = $maxHandSize;
        $this->mana = $mana;
    }

    public static function from(int $playerId, Cards $cards): self
    {
        return new self($playerId, $cards, 7, new Mana(4));
    }

    public function number(): int
    {
        return $this->playerNumber;
    }

    public function cardInHand(int $number): Card
    {
        return $this->cards->inHand()[$number];
    }

    /** @throws NoSuchCard */
    public function cardInPlay(int $number): Card
    {
        try {
            return $this->cards->inPlay()[$number];
        } catch (Throwable $notFound) {
            throw NoSuchCard::atPosition($number, $notFound);
        }
    }

    public function cardsInPlay(): int
    {
        return count($this->cards->inPlay());
    }

    public function attackers(): Cards
    {
        return $this->cards->thatAttack();
    }

    public function drawOpeningHand(MatchId $match): void
    {
        for ($i = 0; $i < $this->maxHandSize; $i++) {
            $this->cards->drawFromTopOfDeck($match, $this->playerNumber);
        }
        foreach ($this->cards->inHand() as $card) {
            $this->happened(...$card->domainEvents());
            $card->eraseEvents();
        }
    }

    public function cannotPay(Mana $cost): bool
    {
        return $this->mana->isLessThan($cost);
    }

    public function pay(Mana $costOfTheCard): void
    {
        $this->mana = $this->mana->minus($costOfTheCard);
    }

    public function counterTheAttackersOf(
        int $attackingPlayer,
        MatchId $match,
        Cards $attackers
    ): void {
        foreach ($this->cards->thatDefend() as $defender) {
            $defender->counterAttack(
                $match,
                $attackers->theOneThatAttacksTheAmbushOf($defender),
                $this->playerNumber,
                $attackingPlayer
            );
            $this->happened(...$defender->domainEvents());
            $defender->eraseEvents();
        }
    }
}
