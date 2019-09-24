<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

use Stratadox\CardGame\Match\Card\Card;
use Stratadox\CardGame\Match\Card\Location;
use Stratadox\CardGame\Match\Mana;
use Stratadox\CardGame\Deck\CardId as CardTemplateId;
use Stratadox\CardGame\Match\Player\PlayerId;

abstract class CardTemplate
{
    private $id;
    private $cost;

    public function __construct(
        CardTemplateId $id,
        Mana $cost
    ) {
        $this->id = $id;
        $this->cost = $cost;
    }

    public function id(): CardTemplateId
    {
        return $this->id;
    }

    public function cost(): Mana
    {
        return $this->cost;
    }

    abstract public function createFor(PlayerId $owner, Location $initialLocation): Card;
}
