<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\CardId;
use Stratadox\CardGame\PlayerId;

final class UnitCard implements Card
{
    private $id;
    private $owner;

    public function __construct(CardId $id, PlayerId $owner)
    {
        $this->id = $id;
        $this->owner = $owner;
    }

    public function id(): CardId
    {
        return $this->id;
    }

    public function putIntoActionOn(Battlefield $battlefield): void
    {
        $battlefield->addUnitFor($this->owner, $this);
    }
}
