<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use function array_key_exists;
use Stratadox\CardGame\Match\MatchId;

class Battlefields
{
    private $battlefields = [];

    public function for(MatchId $match): Battlefield
    {
        if (!array_key_exists($match->id(), $this->battlefields)) {
            $this->battlefields[$match->id()] = Battlefield::untouched();
        }
        return $this->battlefields[$match->id()];
    }

    /** @return Card[] */
    public function cardsInPlay(MatchId $match): array
    {
        return $this->for($match)->cardsInPlay();
    }

    /** @return Card[] */
    public function cardsInPlayFor(int $player, MatchId $match): array
    {
        return $this->for($match)->cardsInPlayFor($player);
    }

    /** @return Card[] */
    public function attackers(MatchId $match): array
    {
        return $this->for($match)->attackers();
    }

    /** @return Card[] */
    public function defenders(MatchId $match): array
    {
        return $this->for($match)->defenders();
    }
}
