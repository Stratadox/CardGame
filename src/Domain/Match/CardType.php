<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class CardType
{
    private $type;

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function spell(): self
    {
        return new self('spell');
    }

    public static function unit(): self
    {
        return new self('unit');
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
