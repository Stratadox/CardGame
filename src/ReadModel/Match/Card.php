<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

interface Card
{
    public function name(): string;
    public function price(): int;
    public function isTheSameAs(Card $theOtherCard): bool;
}
