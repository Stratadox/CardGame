<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use DateTimeInterface;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Stratadox\CardGame\Match\Event\MatchHasBegun;
use Stratadox\CardGame\Match\Event\NextTurnBegan;
use Stratadox\CardGame\Match\Event\StartedMatchForProposal;
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
        $this->happened(...$events);
    }

    public static function fromProposal(
        MatchId $id,
        ProposalId $proposal,
        Decks $decks,
        DateTimeInterface $startTime
    ): self {
        return Match::begin(
            $id,
            new StartedMatchForProposal($id, $proposal),
            new Players(
                Player::from(0, $decks[0]->cards()),
                Player::from(1, $decks[1]->cards())
            ),
            $startTime
        );
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

    /**
     * @throws NotEnoughMana
     * @throws NotYourTurn
     */
    public function playTheCard(
        int $cardNumber,
        int $player,
        DateTimeInterface $when
    ): void {
        $this->turn->mustAllowCardPlaying($player, $when);

        $this->players[$player]->playTheCard($cardNumber, $this->id);

        $this->happened(...$this->players[$player]->domainEvents());
        $this->players[$player]->eraseEvents();
    }

    /**
     * @throws NoSuchCard
     * @throws NotYourTurn
     */
    public function attackWithCard(
        int $cardNumber,
        int $playerNumber,
        DateTimeInterface $when
    ): void {
        $this->turn->mustAllowAttacking($playerNumber, $when);

        $this->players[$playerNumber]->attackWith($cardNumber, $this->id);

        $this->happened(...$this->players[$playerNumber]->domainEvents());
        $this->players[$playerNumber]->eraseEvents();
    }

    /**
     * @throws NoSuchCard
     * @throws NotYourTurn
     */
    public function defendAgainst(
        int $attacker,
        int $defender,
        int $defendingPlayer,
        DateTimeInterface $when
    ): void {
        $this->turn->mustAllowDefending($defendingPlayer, $when);

        $this->players[$defendingPlayer]->defendAgainst($attacker, $defender, $this->id);

        $this->happened(...$this->players[$defendingPlayer]->domainEvents());
        $this->players[$defendingPlayer]->eraseEvents();
    }

    public function drawOpeningHands(): void
    {
        $this->players->drawOpeningHands($this->id);

        foreach ($this->players as $player) {
            $this->happened(...$player->domainEvents());
            $player->eraseEvents();
        }
    }

    /** @throws NotYourTurn */
    public function endCardPlayingPhaseFor(
        int $playerNumber,
        DateTimeInterface $when
    ): void {
        $this->turn = $this->turn->endCardPlayingPhaseFor($playerNumber, $when);
    }

    /** @throws NotYourTurn */
    public function endTurnOf(int $playerNumber, DateTimeInterface $when): void
    {
        if ($this->turn->hasNotHadCombatYet()) {
            $this->letTheCombatBegin($playerNumber, $when);
        }
        $this->turn = $this->turn->beginTheTurnOf(
            $this->players->after($playerNumber),
            $when,
            $playerNumber,
            $this->players[$playerNumber]->hasAttackingUnits()
        );
        $this->happened(
            new NextTurnBegan($this->id, $this->players->after($playerNumber))
        );
    }

    /** @throws NotYourTurn */
    public function letTheCombatBegin(int $defender, DateTimeInterface $when): void
    {
        $this->turn->mustAllowStartingCombat($defender, $when);

        $this->players[$defender]->counterTheAttackersOf(
            $this->players->after($defender),
            $this->id,
            $this->players[$this->players->after($defender)]->attackers()
        );

        // @todo let non-countered units damage defender

        foreach ($this->players as $player) {
            $player->endCombatPhase($this->id);
            $this->happened(...$player->domainEvents());
            $player->eraseEvents();
        }
        $this->turn = $this->turn->endCombatPhase($when);
    }
}
