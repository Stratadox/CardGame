<?php declare(strict_types=1);

namespace Stratadox\CardGame\Deck;

use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;

final class Deck implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $cards;


}
