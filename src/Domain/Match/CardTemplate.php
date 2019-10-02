<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

interface CardTemplate
{
    /** @return MatchEvent[] */
    public function playingEvents(MatchId $match, PlayerId $player): array;
    /** @return MatchEvent[] */
    public function drawingEvents(MatchId $match, PlayerId $player): array;
    /** @return MatchEvent[] */
    public function attackingEvents(MatchId $match, PlayerId $player): array;
    public function playingMove(int $position): Location;
    public function cost(): Mana;
}
