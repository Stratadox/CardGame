<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function assert;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Stratadox\CardGame\Match\Event\CardWasPlayed;
use Stratadox\CardGame\Match\Event\MatchHasBegun;
use Stratadox\CardGame\Match\Event\PlayerDrewOpeningHand;
use Stratadox\CardGame\MatchId;
use Stratadox\CardGame\PlayerId;

/**
 * Aggregate root
 */
final class Match implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $currentPlayer;
    private $battlefield;
    /** @var Player[] */
    private $players = [];

    public function __construct(
        MatchId $id,
        PlayerId $whoBegins,
        Battlefield $battlefield,
        array $events,
        Player ...$players
    ) {
        $this->id = $id;
        $this->battlefield = $battlefield;
        $this->events = $events;
        foreach ($players as $player) {
            $this->players[(string) $player->id()] = $player;
            if ($whoBegins->is($player->id())) {
                $this->currentPlayer = $player;
            }
            $this->events[] = new PlayerDrewOpeningHand(
                $id,
                $player->id(),
                ...$player->cardsInHand()
            );
        }
        assert($this->currentPlayer !== null);
        $this->events[] = new MatchHasBegun($id, $whoBegins);
    }

    public static function fromSetup(
        MatchId $id,
        PlayerId $whoBegins,
        array $events,
        Player ...$players
    ): self {
        return new self($id, $whoBegins, new Battlefield(), $events, ...$players);
    }

    public function id(): MatchId
    {
        return $this->id;
    }

    public function isBeingPlayedBy(PlayerId $aPlayer): bool
    {
        foreach ($this->players as $thePlayer) {
            if ($aPlayer->is($thePlayer->id())) {
                return true;
            }
        }
        return false;
    }

    /** @throws CannotPlayThisCard */
    public function playCard(int $cardNumber, PlayerId $player): void
    {
        if (!$player->is($this->currentPlayer->id())) {
            throw NotYourTurn::cannotPlayCardsYet();
        }

        $this->currentPlayer->playOn($this->battlefield, $cardNumber);

        foreach ($this->currentPlayer->takePlayedCards() as $newlyPlayedCard) {
            $this->events[] = new CardWasPlayed(
                $this->id,
                $player,
                $newlyPlayedCard->id(),
                $newlyPlayedCard->type()
            );
        }
    }
}
