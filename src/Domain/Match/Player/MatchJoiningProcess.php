<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Player;

use function array_reverse;
use function assert;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Deck\CardId;
use Stratadox\CardGame\Match\Deck\DeckIdGenerator;
use Stratadox\CardGame\Match\Deck\Decks;
use Stratadox\CardGame\Match\Deck\DeckTemplate;
use Stratadox\CardGame\Match\Deck\DeckTemplates;
use Stratadox\CardGame\Match\Deck\SpellCardTemplate;
use Stratadox\CardGame\Match\Deck\UnitCardTemplate;
use Stratadox\CardGame\Match\Mana;
use Stratadox\CommandHandling\Handler;

final class MatchJoiningProcess implements Handler
{
    private $players;
    private $decks;
    private $templates;
    private $newDeckId;

    public function __construct(
        Players $players,
        DeckTemplates $templates,
        Decks $decks,
        DeckIdGenerator $deckIdGenerator
    ) {
        $this->players = $players;
        $this->templates = $templates;
        $this->decks = $decks;
        $this->newDeckId = $deckIdGenerator;
    }

    public function handle(object $command): void
    {
        assert($command instanceof StartPlaying);

        // @todo move away from here into own process
        $this->templates->put(new DeckTemplate(
            $command->account(),
            ...array_reverse([
                new UnitCardTemplate(CardId::from('card-id-1'), new Mana(1)),
                new UnitCardTemplate(CardId::from('card-id-2'), new Mana(3)),
                new SpellCardTemplate(CardId::from('card-id-3'), new Mana(4)),
                new UnitCardTemplate(CardId::from('card-id-4'), new Mana(6)),
                new UnitCardTemplate(CardId::from('card-id-5'), new Mana(2)),
                new UnitCardTemplate(CardId::from('card-id-6'), new Mana(5)),
                new UnitCardTemplate(CardId::from('card-id-7'), new Mana(2)),
                new UnitCardTemplate(CardId::from('card-id-8'), new Mana(2)),
                new UnitCardTemplate(CardId::from('card-id-9'), new Mana(2)),
                new UnitCardTemplate(CardId::from('card-id-10'), new Mana(2)),
            ])
        ));
        $this->join($command->player(), $command->account());
    }

    private function join(PlayerId $playerId, AccountId $theirAccount): void
    {
        $template = $this->templates->findFor($theirAccount);
        $deck = $template->prepareFor($playerId, $this->newDeckId->generate());
        // @todo split!
        $this->decks->add($deck);
        $this->players->add(Player::with($playerId, $deck->id()));
    }
}
