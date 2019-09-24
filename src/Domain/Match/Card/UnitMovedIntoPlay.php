<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

use Stratadox\CardGame\Match\Player\PlayerId;
use Stratadox\CardGame\Deck\CardId as TemplateId;

final class UnitMovedIntoPlay implements CardEvent
{
    private $card;
    private $template;
    private $player;

    public function __construct(
        CardId $card,
        TemplateId $template,
        PlayerId $player
    ) {
        $this->card = $card;
        $this->template = $template;
        $this->player = $player;
    }

    public function aggregateId(): CardId
    {
        return $this->card;
    }

    public function card(): CardId
    {
        return $this->aggregateId();
    }

    public function template(): TemplateId
    {
        return $this->template;
    }

    public function player(): PlayerId
    {
        return $this->player;
    }

    public function payload(): array
    {
        return [/*@todo*/];
    }
}
