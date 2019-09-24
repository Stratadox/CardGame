<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CardGame\Match\Deck\DeckId;
use Stratadox\CardGame\Match\Player\NoSuchPlayer;
use Stratadox\CardGame\Match\Player\Player;
use Stratadox\CardGame\Match\Player\PlayerId;
use Stratadox\CardGame\Match\Player\Players;

final class InMemoryPlayers implements Players
{
    /** @var Player[] */
    private $players = [];

    public function add(Player $who): void
    {
        $this->players[(string) $who->id()] = $who;
    }

    public function withId(PlayerId $thePlayer): Player
    {
        if (!isset($this->players[$thePlayer->id()])) {
            throw NoSuchPlayer::weDoNotKnow($thePlayer);
        }
        return $this->players[$thePlayer->id()];
    }

    public function withDeck(DeckId $theDeck): Player
    {
        foreach ($this->players as $player) {
            if ($theDeck->is($player->deck())) {
                return $player;
            }
        }
    }
}
