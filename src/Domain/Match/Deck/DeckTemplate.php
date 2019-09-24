<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

use function array_keys;
use function array_map;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Match\Card\Card;
use Stratadox\CardGame\Match\Card\InDeck;
use Stratadox\CardGame\Match\Player\PlayerId;

final class DeckTemplate
{
    private $owner;
    private $cards;

    public function __construct(AccountId $owner, CardTemplate ...$cards)
    {
        $this->owner = $owner;
        $this->cards = $cards;
    }

    public function owner(): AccountId
    {
        return $this->owner;
    }

    public function prepareFor(PlayerId $player, DeckId $deckId): Deck
    {
        return new Deck($deckId, $player, ...array_map(
            function (CardTemplate $template, int $position) use ($player, $deckId): Card {
                return $template->createFor($player, new InDeck($deckId, $position));
            },
            $this->cards,
            array_keys($this->cards)
        ));
    }
}
