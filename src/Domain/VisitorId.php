<?php declare(strict_types=1);

namespace Stratadox\CardGame;

final class VisitorId
{
    private $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function from($id): self
    {
        return new self((string) $id);
    }

    public function is(VisitorId $theOther): bool
    {
        return (string) $this === (string) $theOther;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
