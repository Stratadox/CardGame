<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;

final class Card implements DomainEventRecorder
{
    use DomainEventRecording;

    private $owner;
    private $location;
    private $template;

    private function __construct(int $owner, Location $location, CardTemplate $template)
    {
        $this->owner = $owner;
        $this->location = $location;
        $this->template = $template;
    }

    public static function inDeck(
        int $owner,
        int $position,
        CardTemplate $template
    ): self {
        return new self($owner, Location::inDeck($position), $template);
    }

    public function owner(): int
    {
        return $this->owner;
    }

    public function isInDeck(): bool
    {
        return $this->location->isInDeck();
    }

    public function isInHand(): bool
    {
        return $this->location->isInHand();
    }

    public function isInPlay(): bool
    {
        return $this->location->isInPlay();
    }

    public function isAttacking(): bool
    {
        return $this->location->isAttacking();
    }

    public function isDefending(): bool
    {
        return $this->location->isDefending();
    }

    public function isAttackingThe(Card $defender): bool
    {
        return $this->location->isAttackingThe($defender->location);
    }

    public function hasHigherPositionThan(Card $other): bool
    {
        return $this->location->hasHigherPositionThan($other->location);
    }

    public function draw(MatchId $match, int $position, int $player): void
    {
        $this->location = $this->location->toHand($position);
        $this->happened(...$this->template->drawingEvents($match, $player));
    }

    public function play(MatchId $match, int $position, int $player): void
    {
        $this->location = $this->template->playingMove($position);
        $this->happened(...$this->template->playingEvents($match, $player));
    }

    public function sendToAttack(MatchId $match, int $position, int $player): void
    {
        $this->location = $this->template->attackingMove($position);
        $this->happened(...$this->template->attackingEvents($match, $player));
    }

    public function sendToDefendAgainst(MatchId $match, int $position, int $player): void
    {
        $this->location = $this->template->defendingMove($position);
        $this->happened(...$this->template->defendingEvents($match, $player));
    }

    public function counterAttack(MatchId $match, Card $attacker): void
    {
        // kill 'm for now... @todo combat mechanics
        $attacker->location = Location::inVoid();
        $this->happened(...$attacker->template->dyingEvents($match));
    }

    public function cost(): Mana
    {
        return $this->template->cost();
    }
}
