<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\CardId;

interface Card
{
    public function id(): CardId;
    public function putIntoActionOn(Battlefield $battlefield): void;
    public function type(): CardType;
    public function cost(): Mana;
}
