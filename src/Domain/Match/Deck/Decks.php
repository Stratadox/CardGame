<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

use Stratadox\CardGame\Match\Player\PlayerId;

// @todo refactor to interface
class Decks
{
    /** @var Deck[] */
    private $decks = [];

    public function byId(DeckId $deck): Deck
    {
        return $this->decks[$deck->id()];
    }

    public function add(Deck $deck): void
    {
        $this->decks[(string) $deck->id()] = $deck;
    }

    public function forPlayer(PlayerId $player): Deck
    {
        foreach ($this->decks as $deck) {
            if ($player->is($deck->owner())) {
                return $deck;
            }
        }
    }
}
