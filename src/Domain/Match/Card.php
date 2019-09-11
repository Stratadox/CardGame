<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

interface Card
{
    public function id(): CardId;
    public function putIntoActionOn(Battlefield $battlefield): void;
    public function type(): CardType;
    public function cost(): Mana;
}
