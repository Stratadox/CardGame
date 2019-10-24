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

    /** @throws NoSuchCard */
    public function cardInPlay(int $number): Card
    {
        try {
            return $this->cards->inPlay()[$number];
        } catch (Throwable $notFound) {
            throw NoSuchCard::atPosition($number, $notFound);
        }
    }

    /** @throws NotEnoughMana */
    public function playTheCard(int $cardNumber, MatchId $match): void
    {
        $card = $this->cards->inHand()[$cardNumber];

        if ($this->mana->isLessThan($card->cost())) {
            throw NotEnoughMana::toPlayThatCard();
        }

        $this->mana = $this->mana->minus($card->cost());
        $card->play($match, count($this->cards->inPlay()), $this->playerNumber);

        $this->happened(...$card->domainEvents());
        $card->eraseEvents();
    }

    /** @throws NoSuchCard */
    public function attackWith(int $cardNumber, MatchId $match): void
    {
        $card = $this->cardInPlay($cardNumber);
        $card->sendToAttack($match, count($this->attackers()), $this->playerNumber);

        $this->happened(...$card->domainEvents());
        $card->eraseEvents();
    }

    /** @throws NoSuchCard */
    public function defendAgainst(
        int $attacker,
        int $defender,
        MatchId $match
    ): void {
        $card = $this->cardInPlay($defender);
        $card->sendToDefendAgainst($match, $attacker, $this->playerNumber);

        $this->happened(...$card->domainEvents());
        $card->eraseEvents();
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
