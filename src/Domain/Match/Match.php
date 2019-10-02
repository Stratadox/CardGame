<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_keys;
use function array_map;
use DateTimeInterface;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Stratadox\CardGame\Proposal\ProposalId;

final class Match implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $turn;
    private $players;

    private function __construct(
        MatchId $id,
        Turn $turn,
        Players $players,
        array $events
    ) {
        $this->id = $id;
        $this->turn = $turn;
        $this->players = $players;
        $this->events = $events;
    }

    public static function fromProposal(
        MatchId $id,
        ProposalId $proposal,
        Decks $decks,
        DateTimeInterface $startTime,
        PlayerId ...$players
    ): self {
        return Match::begin(
            $id,
            new StartedMatchForProposal($id, $proposal, ...$players),
            new Players(...self::players($decks, ...$players)),
            $startTime
        );
    }

    private static function players(Decks $decks, PlayerId ...$ids): array
    {
        return array_map(function (PlayerId $playerId, int $i) use ($decks): Player {
            return Player::from($playerId, $decks[$i]->cardsFor($playerId));
        }, $ids, array_keys($ids));
    }

    private static function begin(
        MatchId $id,
        MatchEvent $creationEvent,
        Players $players,
        DateTimeInterface $startTime
    ): Match {
        $whoBegins = $players->pickRandomId();
        return new Match(
            $id,
            new Turn($whoBegins, $startTime),
            $players,
            [$creationEvent, new MatchHasBegun($id, $whoBegins)]
        );
    }

    public function id(): MatchId
    {
        return $this->id;
    }

    public function playTheCard(
        int $cardNumber,
        PlayerId $thePlayer,
        DateTimeInterface $when
    ): void {
        $this->putIntoPlay($this->players->withId($thePlayer), $cardNumber, $when);
    }

    public function attackWithCard(
        int $cardNumber,
        PlayerId $thePlayer,
        DateTimeInterface $when
    ): void {
        try {
            $this->moveToAttack(
                $this->players->withId($thePlayer),
                $cardNumber,
                $when
            );
        } catch (NoSuchCard $noSuchCard) {
            // events += tried to attack with card
        }
    }

    public function drawOpeningHands(): void
    {
        $this->players->drawOpeningHands($this->id);

        foreach ($this->players as $player) {
            $this->addEvents(...$player->domainEvents());
            $player->eraseEvents();
        }
    }

    public function endCardPlayingPhaseFor(PlayerId $thePlayer): void
    {
        $this->turn = $this->turn->endCardPlayingPhaseFor($thePlayer);
    }

    private function putIntoPlay(
        Player $thePlayer,
        int $cardNumber,
        DateTimeInterface $when
    ): void {
        if ($this->turn->prohibitsPlaying($thePlayer->cardInHand($cardNumber), $when)) {
            // events += new CannotPlay($theCard)?
            return;
        }

        $this->play($thePlayer->cardInHand($cardNumber), $thePlayer);
    }

    /** @throws NoSuchCard */
    private function moveToAttack(
        Player $thePlayer,
        int $cardNumber,
        DateTimeInterface $when
    ): void {
        if ($this->turn->prohibitsAttacking($thePlayer->cardInPlay($cardNumber), $when)) {
            // events += new CannotPlay($theCard)?
            return;
        }

        $this->attackWith($thePlayer->cardInPlay($cardNumber), $thePlayer);
    }

    private function play(Card $theCard, Player $thePlayer): void
    {
        if ($thePlayer->cannotPay($theCard->cost())) {
            // events += new CannotPayFor($theCard)?
            return;
        }

        $thePlayer->pay($theCard->cost());
        $theCard->play($this->id, $thePlayer->cardsInPlay(), $thePlayer->id());

        $this->addEvents(...$theCard->domainEvents());
        $theCard->eraseEvents();
    }

    private function attackWith(Card $theCard, Player $thePlayer): void
    {
        $theCard->sendToAttack($this->id, $thePlayer->id());

        $this->addEvents(...$theCard->domainEvents());
        $theCard->eraseEvents();
    }

    private function addEvents(MatchEvent ...$newEvents): void
    {
        foreach ($newEvents as $newEvent) {
            $this->events[] = $newEvent;
        }
    }
}
