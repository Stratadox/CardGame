<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_merge;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;

final class Card implements DomainEventRecorder
{
    use DomainEventRecording;

    private $owner;
    private $location;
    private $template;

    private function __construct(PlayerId $owner, Location $location, CardTemplate $template)
    {
        $this->owner = $owner;
        $this->location = $location;
        $this->template = $template;
    }

    public static function inDeck(
        PlayerId $owner,
        int $position,
        CardTemplate $template
    ): self {
        return new self($owner, Location::inDeck($position), $template);
    }

    public static function inHand(
        PlayerId $owner,
        int $position,
        CardTemplate $template
    ): self {
        return new self($owner, Location::inHand($position), $template);
    }

    public function owner(): PlayerId
    {
        return $this->owner;
    }

    public function isInHand(): bool
    {
        return $this->location->isInHand();
    }

    public function isInPlay(): bool
    {
        return $this->location->isInPlay();
    }

    public function isInDeck(): bool
    {
        return $this->location->isInDeck();
    }

    public function hasHigherPositionThan(Card $other): bool
    {
        return $this->location->hasHigherPositionThan($other->location);
    }

    public function play(MatchId $match, int $position, PlayerId $player): void
    {
        $this->location = $this->template->playingMove($position);
        $this->events = array_merge($this->events, $this->template->playingEvents($match, $player));
    }

    public function draw(MatchId $match, int $position, PlayerId $player): void
    {
        $this->location = $this->location->toHand($position);
        $this->events = array_merge($this->events, $this->template->drawingEvents($match, $player));
    }

    public function cost(): Mana
    {
        return $this->template->cost();
    }
}
