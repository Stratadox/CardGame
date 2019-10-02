<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_merge;
use function count;
use RuntimeException;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Throwable;

final class Player implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $cards;
    private $maxHandSize;
    private $mana;

    public function __construct(
        PlayerId $id,
        Cards $cards,
        int $maxHandSize,
        Mana $mana
    ) {
        $this->id = $id;
        $this->cards = $cards;
        $this->maxHandSize = $maxHandSize;
        $this->mana = $mana;
    }

    public static function from(PlayerId $playerId, Cards $cards): self
    {
        return new self($playerId, $cards, 7, new Mana(4));
    }

    public function id(): PlayerId
    {
        return $this->id;
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

    public function drawOpeningHand(MatchId $match): void
    {
        for ($i = 0; $i < $this->maxHandSize; $i++) {
            $this->cards->drawFromTopOfDeck($match, $this->id);
        }
        foreach ($this->cards->inHand() as $card) {
            $this->events = array_merge($this->events, $card->domainEvents());
            $card->eraseEvents();
        }
    }

    public function cannotPay(Mana $theCosts): bool
    {
        return $this->mana->isLessThan($theCosts);
    }

    public function pay(Mana $theCostOfTheCard): void
    {
        $this->mana = $this->mana->minus($theCostOfTheCard);
    }
}
