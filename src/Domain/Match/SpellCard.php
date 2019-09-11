<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class SpellCard implements Card
{
    private $id;
    private $cost;

    public function __construct(CardId $id, PlayerId $owner, Mana $cost)
    {
        $this->id = $id;
        $this->cost = $cost;
    }

    public function id(): CardId
    {
        return $this->id;
    }

    public function putIntoActionOn(Battlefield $battlefield): void
    {
    }

    public function type(): CardType
    {
        return CardType::spell();
    }

    public function cost(): Mana
    {
        return $this->cost;
    }
}
