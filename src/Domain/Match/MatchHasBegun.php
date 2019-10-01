<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\Match\MatchEvent;
use Stratadox\CardGame\Match\MatchId;
use Stratadox\CardGame\Match\PlayerId;

final class MatchHasBegun implements MatchEvent
{
    private $match;
    private $whoBegins;

    public function __construct(MatchId $match, PlayerId $whoBegins)
    {
        $this->match = $match;
        $this->whoBegins = $whoBegins;
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }

    public function whoBegins(): PlayerId
    {
        return $this->whoBegins;
    }

    public function payload(): array
    {
        return [];
    }
}
