<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Player;

use Stratadox\CardGame\Match\Deck\DeckId;

interface Players
{
    public function add(Player $who): void;
    /** @throws NoSuchPlayer */
    public function withId(PlayerId $thePlayer): Player;
    /** @throws NoSuchPlayer */
    public function withDeck(DeckId $theDeck): Player;
}
