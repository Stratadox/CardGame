<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\PlayerId;

final class Player
{
    private $id;
    private $deck;

    public function __construct(PlayerId $id, Deck $deck)
    {
        $this->id = $id;
        $this->deck = $deck;
    }

    public static function with(PlayerId $id, Deck $deck): self
    {
        return new self($id, $deck);
    }


}
