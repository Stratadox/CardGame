<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class MatchHasBegun implements MatchEvent
{
    private $match;
    private $whoBegins;

    public function __construct(MatchId $match, int $whoBegins)
    {
        $this->match = $match;
        $this->whoBegins = $whoBegins;
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }

    public function whoBegins(): int
    {
        return $this->whoBegins;
    }
}
