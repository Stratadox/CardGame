<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

use Stratadox\CardGame\DomainEvent;

interface CardEvent extends DomainEvent
{
    public function aggregateId(): CardId;
}
