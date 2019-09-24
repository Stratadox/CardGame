<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Player;

use Stratadox\CardGame\DomainEvent;

interface PlayerEvent extends DomainEvent
{
    public function aggregateId(): PlayerId;
}
