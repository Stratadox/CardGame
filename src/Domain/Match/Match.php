<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_keys;
use function array_map;
use function count;
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
            return Player::from($playerId, $decks[$i]->cards());
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

    /**
     * @throws NotEnoughMana
     * @throws NotYourTurn
     */
    public function playTheCard(
        int $cardNumber,
        int $player,
        DateTimeInterface $when
    ): void {
        if ($this->turn->prohibitsPlaying($player, $when)) {
            throw NotYourTurn::cannotPlayCards();
        }

        $this->play(
            $this->players[$player]->cardInHand($cardNumber),
            $this->players[$player]
        );
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
        if ($this->turn->prohibitsAttacking($playerNumber, $when)) {
            throw NotYourTurn::cannotAttack();
        }

        $this->attackWith(
            $this->players[$playerNumber]->cardInPlay($cardNumber),
            $this->players[$playerNumber]
        );
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
        if ($this->turn->prohibitsDefending($defendingPlayer, $when)) {
            throw NotYourTurn::cannotDefend();
        }

        $this->defendWith(
            $this->players[$defendingPlayer]->cardInPlay($defender),
            $attacker,
            $this->players[$defendingPlayer]
        );
    }

    public function drawOpeningHands(): void
    {
        $this->players->drawOpeningHands($this->id);

        foreach ($this->players as $player) {
            $this->happened(...$player->domainEvents());
            $player->eraseEvents();
        }
    }

    public function endCardPlayingPhaseFor(int $playerNumber): void
    {
        $this->turn = $this->turn->endCardPlayingPhaseFor($playerNumber);
    }

    public function endTurnOf(int $playerNumber, DateTimeInterface $when): void
    {
        $this->beginNextTurnFor($this->players->after($playerNumber), $when);
    }

    /** @throws NotYourTurn */
    public function letTheCombatBegin(int $defender, DateTimeInterface $when): void
    {
        if ($this->turn->prohibitsStartingCombat($defender, $when)) {
            throw NotYourTurn::cannotStartCombat();
        }

        $this->players[$defender]->counterTheAttackersOf(
            $this->playerThatGoesAfter($defender),
            $this->id,
            $this->players[$this->playerThatGoesAfter($defender)]->attackers()
        );
        $this->turn = $this->turn->endCombatPhase();

        $this->happened(...$this->players[$defender]->domainEvents());
        $this->players[$defender]->eraseEvents();
    }

    /** @throws NotEnoughMana */
    private function play(Card $card, Player $player): void
    {
        if ($player->cannotPay($card->cost())) {
            throw NotEnoughMana::toPlayThatCard();
        }

        $player->pay($card->cost());
        $card->play($this->id, $player->cardsInPlay(), $player->number());

        $this->happened(...$card->domainEvents());
        $card->eraseEvents();
    }

    private function attackWith(Card $card, Player $player): void
    {
        $card->sendToAttack($this->id, count($player->attackers()), $player->number());

        $this->happened(...$card->domainEvents());
        $card->eraseEvents();
    }

    private function defendWith(
        Card $defender,
        int $attackerPosition,
        Player $player
    ): void {
        $defender->sendToDefendAgainst($this->id, $attackerPosition, $player->number());

        $this->happened(...$defender->domainEvents());
        $defender->eraseEvents();
    }

    private function playerThatGoesAfter(int $player): int
    {
        return $this->players->after($player);
    }

    private function beginNextTurnFor(
        int $nextPlayer,
        DateTimeInterface $sinceNow
    ): void {
        $this->turn = $this->turn->of($nextPlayer, $sinceNow);
        $this->happened(new NextTurnBegan($this->id, $nextPlayer));
    }
}
