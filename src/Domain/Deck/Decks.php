<?php declare(strict_types=1);

namespace Stratadox\CardGame\Deck;

interface Decks
{
    public function add(Deck $theDeck): void;
    public function initial(): Deck;
}
