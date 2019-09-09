<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\CardId;
use Stratadox\CardGame\Match\CardType;
use Stratadox\CardGame\PlayerId;

final class SpellCard implements Card
{
    private $id;

    public function __construct(CardId $id, PlayerId $owner)
    {
        $this->id = $id;
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
}
