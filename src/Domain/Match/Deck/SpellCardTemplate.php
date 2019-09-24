<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

use function assert;
use Stratadox\CardGame\Match\Card\Card;
use Stratadox\CardGame\Match\Card\CardId;
use Stratadox\CardGame\Match\Card\InDeck;
use Stratadox\CardGame\Match\Card\Location;
use Stratadox\CardGame\Match\Card\SpellCard;
use Stratadox\CardGame\Match\Mana;
use Stratadox\CardGame\Match\Player\PlayerId;

final class SpellCardTemplate extends CardTemplate
{
    public function createFor(PlayerId $owner, Location $initialLocation): Card
    {
        assert($initialLocation instanceof InDeck);

        return new SpellCard(
            CardId::from(
                sprintf('Card #%d of %s', $initialLocation->position(), $owner)
            ),
            $owner,
            $this->id(),
            $initialLocation,
            new Mana(2)
        );
    }
}
