<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Card\Cards;
use Stratadox\CommandHandling\Handler;

final class CardPreparationProcess implements Handler
{
    private $decks;
    private $cardsInTheMatch;
    private $eventBag;

    public function __construct(Decks $decks, Cards $cards, EventBag $eventBag)
    {
        $this->decks = $decks;
        $this->cardsInTheMatch = $cards;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof PrepareCard);

        $this->addCardToGame(
            $this->decks->byId($command->deck()),
            $command->cardOffset()
        );
    }

    public function addCardToGame(Deck $theDeck, int $thePosition): void
    {
        // @todo refactor to let a domain entity create the events?
        try {
            $this->cardsInTheMatch->add($theDeck->cardAt($thePosition));

            $this->eventBag->add(new AddedTheCardToTheMatch($theDeck->id(), $thePosition));
        } catch (CardNotInDeck $couldNotAddItTOTheGame) {
            $this->eventBag->add(new AddedAllCardsToTheMatch($theDeck->id()));
        }
    }
}
