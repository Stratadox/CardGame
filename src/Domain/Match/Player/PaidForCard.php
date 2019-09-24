<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Player;

use Stratadox\CardGame\Match\Card\CardId;

final class PaidForCard implements PlayerEvent
{
    private $player;
    private $card;

    public function __construct(PlayerId $player, CardId $card)
    {
        $this->player = $player;
        $this->card = $card;
    }

    public function aggregateId(): PlayerId
    {
        return $this->player;
    }

    public function player(): PlayerId
    {
        return $this->aggregateId();
    }

    public function card(): CardId
    {
        return $this->card;
    }

    public function payload(): array
    {
        return [];
    }
}
