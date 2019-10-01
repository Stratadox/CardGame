<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\Deck\CardId;

interface CardTemplate
{
    /** @return MatchEvent[] */
    public function playingEvents(MatchId $match, PlayerId $player): array;
    /** @return MatchEvent[] */
    public function drawingEvents(MatchId $match, PlayerId $player): array;
    public function playingMove(int $position): Location;
    public function cardIdentifier(): CardId;
    public function cost(): Mana;
}
