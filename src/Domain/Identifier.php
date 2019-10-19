<?php declare(strict_types=1);

namespace Stratadox\CardGame;

abstract class Identifier
{
    private $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    /** @return static */
    public static function from($id): self
    {
        return new static((string) $id);
    }

    public function is(self $that): bool
    {
        return (string) $this === (string) $that;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
