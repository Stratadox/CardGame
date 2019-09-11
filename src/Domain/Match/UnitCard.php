<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class UnitCard implements Card
{
    private $id;
    private $owner;
    private $cost;

    public function __construct(CardId $id, PlayerId $owner, Mana $cost)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->cost = $cost;
    }

    public function id(): CardId
    {
        return $this->id;
    }

    public function putIntoActionOn(Battlefield $battlefield): void
    {
        $battlefield->addUnitFor($this->owner, $this);
    }

    public function type(): CardType
    {
        return CardType::unit();
    }

    public function cost(): Mana
    {
        return $this->cost;
    }
}
