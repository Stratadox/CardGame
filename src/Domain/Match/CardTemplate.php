<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

interface CardTemplate
{
    /** @return MatchEvent[] */
    public function playingEvents(MatchId $match, int $player): array;
    /** @return MatchEvent[] */
    public function drawingEvents(MatchId $match, int $player): array;
    /** @return MatchEvent[] */
    public function attackingEvents(MatchId $match, int $player): array;
    /** @return MatchEvent[] */
    public function defendingEvents(MatchId $match, int $player): array;
    /** @return MatchEvent[] */
    public function dyingEvents(MatchId $match, int $player): array;
    public function playingMove(int $position): Location;
    public function attackingMove(int $position): Location;
    public function defendingMove(int $position): Location;
    public function cost(): Mana;
}
