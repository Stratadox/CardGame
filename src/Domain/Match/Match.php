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
                $cardNumber,
                $this->players->withId($thePlayer),
                $when
            );
        } catch (NoSuchCard $noSuchCard) {
            //@todo this happened: tried to attack with unknown card
        }
    }

    public function defendAgainst(
        int $attacker,
        int $defender,
        PlayerId $attackingPlayer,
        DateTimeInterface $when
    ): void {
        try {
            $this->moveToDefend(
                $attacker,
                $defender,
                $this->players->withId($attackingPlayer),
                $when
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

    public function endCardPlayingPhaseFor(PlayerId $thePlayer): void
    {
        $this->turn = $this->turn->endCardPlayingPhaseFor($thePlayer);
    }

    public function endTurnOf(PlayerId $thePlayer, DateTimeInterface $when): void
    {
        $this->beginNextTurnFor($this->playerThatGoesAfter($thePlayer), $when);
    }

    public function letTheCombatBegin(
        PlayerId $theDefendingPlayer,
        DateTimeInterface $when
    ): void {
        // @todo check with turn if time for combat
        $this->attackTheAttackers(
            $this->players->withId($theDefendingPlayer),
            $this->players->withId($this->playerThatGoesAfter($theDefendingPlayer))
        );
    }

    private function putIntoPlay(
        Player $thePlayer,
        int $cardNumber,
        DateTimeInterface $when
    ): void {
        if ($this->turn->prohibitsPlaying($thePlayer->cardInHand($cardNumber), $when)) {
            $this->happened(new TriedPlayingCardOutOfTurn($this->id, $thePlayer->id()));
            return;
        }

        $this->play($thePlayer->cardInHand($cardNumber), $thePlayer);
    }

    /** @throws NoSuchCard */
    private function moveToAttack(
        int $cardNumber,
        Player $thePlayer,
        DateTimeInterface $when
    ): void {
        if ($this->turn->prohibitsAttacking($thePlayer->cardInPlay($cardNumber), $when)) {
            // @todo this happened: tried attacking out of turn
            return;
        }

        $this->attackWith($thePlayer->cardInPlay($cardNumber), $thePlayer);
    }

    /** @throws NoSuchCard */
    private function moveToDefend(
        int $attacker,
        int $defender,
        Player $thePlayer,
        DateTimeInterface $when
    ): void {
        // @todo check if defending out of turn ($when)
        $this->defendWith($thePlayer->cardInPlay($defender), $attacker, $thePlayer);
    }

    private function play(Card $theCard, Player $thePlayer): void
    {
        if ($thePlayer->cannotPay($theCard->cost())) {
            $this->happened(new PlayerDidNotHaveTheMana($this->id, $thePlayer->id()));
            return;
        }

        $thePlayer->pay($theCard->cost());
        $theCard->play($this->id, $thePlayer->cardsInPlay(), $thePlayer->id());

        $this->happened(...$theCard->domainEvents());
        $theCard->eraseEvents();
    }

    private function attackWith(Card $theCard, Player $thePlayer): void
    {
        $theCard->sendToAttack($this->id, count($thePlayer->attackers()), $thePlayer->id());

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
            $thePlayer->id()
        );

        $this->happened(...$theDefender->domainEvents());
        $theDefender->eraseEvents();
    }

    private function attackTheAttackers(
        Player $theDefendingPlayer,
        Player $theAttackingPlayer
    ): void {
        $theDefendingPlayer->counterTheAttackers(
            $this->id,
            $theAttackingPlayer->attackers()
        );

        $this->happened(...$theDefendingPlayer->domainEvents());
        $theDefendingPlayer->eraseEvents();
    }

    private function playerThatGoesAfter(PlayerId $thePlayer): PlayerId
    {
        return $this->players->after($thePlayer);
    }

    private function beginNextTurnFor(
        PlayerId $theNextPlayer,
        DateTimeInterface $sinceNow
    ): void {
        $this->turn = $this->turn->of($theNextPlayer, $sinceNow);
        $this->happened(new NextTurnBegan($this->id, $theNextPlayer));
    }
}
