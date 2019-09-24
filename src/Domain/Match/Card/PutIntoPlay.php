<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

final class PutIntoPlay
{
    private $card;

    private function __construct(CardId $card)
    {
        $this->card = $card;
    }

    public static function the(CardId $card): self
    {
        return new self($card);
    }

    public function card(): CardId
    {
        return $this->card;
    }
}
