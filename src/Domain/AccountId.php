<?php declare(strict_types=1);

namespace Stratadox\CardGame;

final class AccountId
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

    public function is(AccountId $theOther): bool
    {
        return (string) $this === (string) $theOther;
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
