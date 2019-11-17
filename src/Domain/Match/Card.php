<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;

final class Card implements DomainEventRecorder
{
    use DomainEventRecording;

    private $location;
    private $template;
    private $offset;

    private function __construct(
        Location $location,
        CardTemplate $template,
        int $offset
    ) {
        $this->location = $location;
        $this->template = $template;
        $this->offset = $offset;
    }

    public static function inDeck(int $position, CardTemplate $template): self
    {
        return new self(Location::inDeck($position), $template, $position);
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
        $this->happened(
            ...$this->template->drawingEvents($match, $player, $this->offset)
        );
    }

    public function play(MatchId $match, int $position, int $player): void
    {
        $this->location = $this->template->playingMove($position);
        $this->happened(
            ...$this->template->playingEvents($match, $player, $this->offset)
        );
    }

    public function sendToAttack(
        MatchId $match,
        int $position,
        int $player
    ): void {
        $this->location = $this->template->attackingMove($position);
        $this->happened(
            ...$this->template->attackingEvents($match, $player, $this->offset)
        );
    }

    public function sendToDefendAgainst(
        MatchId $match,
        int $position,
        int $player
    ): void {
        $this->location = $this->template->defendingMove($position);
        $this->happened(
            ...$this->template->defendingEvents($match, $player, $this->offset)
        );
    }

    public function counterAttack(
        MatchId $match,
        Card $attacker,
        int $blockingPlayer,
        int $attackingPlayer
    ): void {
        // @todo better combat mechanics
        if ($this->cost() > $attacker->cost()) {
            $attacker->location = Location::inVoid();
            $this->happened(...$attacker->template->dyingEvents(
                $match,
                $attackingPlayer,
                $attacker->offset
            ));
        } else {
            $this->location = Location::inVoid();
            $this->happened(...$this->template->dyingEvents(
                $match,
                $blockingPlayer,
                $this->offset
            ));
        }
    }

    public function regroup(MatchId $match, int $position, int $player): void
    {
        $this->location = $this->template->regroupingMove($position);
        $this->happened(
            ...$this->template->regroupingEvents($match, $player, $this->offset)
        );
    }

    public function cost(): Mana
    {
        return $this->template->cost();
    }
}
