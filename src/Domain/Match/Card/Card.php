<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

use function count;
use Stratadox\CardGame\Deck\CardId as CardTemplateId;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Stratadox\CardGame\Match\Deck\DeckId;
use Stratadox\CardGame\Match\Mana;
use Stratadox\CardGame\Match\Player\PlayerId;

abstract class Card implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $owner;
    private $template;
    protected $location;
    private $cost;

    public function __construct(
        CardId $id,
        PlayerId $owner,
        CardTemplateId $template,
        Location $location,
        Mana $cost
    ) {
        $this->id = $id;
        $this->owner = $owner;
        $this->template = $template;
        $this->location = $location;
        $this->cost = $cost;
    }

    public function id(): CardId
    {
        return $this->id;
    }

    public function cost(): Mana
    {
        return $this->cost;
    }

    public function isInDeck(DeckId $theDeck): bool
    {
        return $this->location->isInDeck($theDeck);
    }

    public function isInHandOf(PlayerId $thePlayer): bool
    {
        return $this->location->isInHandOf($thePlayer);
    }

    public function position(): ?int
    {
        return $this->location->position();
    }

    public function draw(Card ...$currentlyInHand): void
    {
        $this->location = $this->location->toHand($this->owner, count($currentlyInHand));
        $this->events[] = new CardWasDrawn($this->id(), $this->template, $this->owner);
    }

    protected function owner(): PlayerId
    {
        return $this->owner;
    }

    protected function template(): CardTemplateId
    {
        return $this->template;
    }

    abstract public function play(): void;
}
