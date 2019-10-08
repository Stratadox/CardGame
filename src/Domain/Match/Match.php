<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_keys;
use function array_map;
use function count;
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

    // @todo simplify match construction / move to factory
    public static function fromProposal(
        MatchId $id,
        ProposalId $proposal,
        Decks $decks,
        DateTimeInterface $startTime,
        int ...$players
    ): self {
        return Match::begin(
            $id,
            new StartedMatchForProposal($id, $proposal, ...$players),
            new Players(...self::players($decks, ...$players)),
            $startTime
        );
    }

    private static function players(Decks $decks, int ...$ids): array
    {
        return array_map(function (int $playerId, int $i) use ($decks): Player {
            return Player::from($playerId, $decks[$i]->cardsFor($playerId));
        }, $ids, array_keys($ids));
    }

    private static function begin(
        MatchId $id,
        MatchEvent $creationEvent,
        Players $players,
        DateTimeInterface $startTime
    ): Match {
        $whoBegins = $players->pickRandom();
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
        int $thePlayer,
        DateTimeInterface $when
    ): void {
        if ($this->turn->prohibitsPlaying(
            $this->players[$thePlayer]->cardInHand($cardNumber),
            $when
        )) {
            $this->happened(
                new TriedPlayingCardOutOfTurn($this->id, $this->players[$thePlayer]->number())
            );
            return;
        }

        $this->play($this->players[$thePlayer]->cardInHand($cardNumber), $this->players[$thePlayer]);
    }

    public function attackWithCard(
        int $cardNumber,
        int $thePlayer,
        DateTimeInterface $when
    ): void {
        try {
            $card = $this->players[$thePlayer]->cardInPlay($cardNumber);
        } catch (NoSuchCard $noSuchCard) {
            //@todo this happened: tried to attack with unknown card
            return;
        }
        $this->attackWith($card, $this->players[$thePlayer]);
    }

    public function defendAgainst(
        int $attacker,
        int $defender,
        int $attackingPlayer,
        DateTimeInterface $when
    ): void {
        try {
            $this->defendWith(
                $this->players[$attackingPlayer]->cardInPlay($defender),
                $attacker,
                $this->players[$attackingPlayer]
            );
        } catch (NoSuchCard $noSuchCard) {
            //@todo this happened: tried to attack with unknown card
        }
    }

    public function drawOpeningHands(): void
    {
        $this->players->drawOpeningHands($this->id);

        foreach ($this->players as $player) {
            $this->happened(...$player->domainEvents());
            $player->eraseEvents();
        }
    }

    public function endCardPlayingPhaseFor(int $thePlayer): void
    {
        $this->turn = $this->turn->endCardPlayingPhaseFor($thePlayer);
    }

    public function endTurnOf(int $thePlayer, DateTimeInterface $when): void
    {
        $this->beginNextTurnFor($this->players->after($thePlayer), $when);
    }

    public function letTheCombatBegin(int $defender, DateTimeInterface $when): void
    {
        // @todo check with turn if time for combat
        $this->players[$defender]->counterTheAttackers(
            $this->id,
            $this->players[$this->playerThatGoesAfter($defender)]->attackers()
        );

        $this->happened(...$this->players[$defender]->domainEvents());
        $this->players[$defender]->eraseEvents();
    }

    private function play(Card $theCard, Player $thePlayer): void
    {
        if ($thePlayer->cannotPay($theCard->cost())) {
            $this->happened(new PlayerDidNotHaveTheMana($this->id, $thePlayer->number()));
            return;
        }

        $thePlayer->pay($theCard->cost());
        $theCard->play($this->id, $thePlayer->cardsInPlay(), $thePlayer->number());

        $this->happened(...$theCard->domainEvents());
        $theCard->eraseEvents();
    }

    private function attackWith(Card $theCard, Player $thePlayer): void
    {
        $theCard->sendToAttack($this->id, count($thePlayer->attackers()), $thePlayer->number());

        $this->happened(...$theCard->domainEvents());
        $theCard->eraseEvents();
    }

    private function defendWith(
        Card $theDefender,
        int $theAttacker,
        Player $thePlayer
    ): void {
        $theDefender->sendToDefendAgainst(
            $this->id,
            $theAttacker,
            $thePlayer->number()
        );

        $this->happened(...$theDefender->domainEvents());
        $theDefender->eraseEvents();
    }

    private function playerThatGoesAfter(int $thePlayer): int
    {
        return $this->players->after($thePlayer);
    }

    private function beginNextTurnFor(
        int $theNextPlayer,
        DateTimeInterface $sinceNow
    ): void {
        $this->turn = $this->turn->of($theNextPlayer, $sinceNow);
        $this->happened(new NextTurnBegan($this->id, $theNextPlayer));
    }
}
