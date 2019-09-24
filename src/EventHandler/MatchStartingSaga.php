<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Card\CardWasDrawn;
use Stratadox\CardGame\Match\Deck\AddedAllCardsToTheMatch;
use Stratadox\CardGame\Match\Deck\AddedTheCardToTheMatch;
use Stratadox\CardGame\Match\Deck\DeckHasBeenShuffled;
use Stratadox\CardGame\Match\Deck\PrepareCard;
use Stratadox\CardGame\Match\Deck\ShuffleDeck;
use Stratadox\CardGame\Match\Card\DrawCard;
use Stratadox\CardGame\Match\Match\MatchId;
use Stratadox\CardGame\Match\Match\OkayLetsGo;
use Stratadox\CardGame\Match\Player\PlayerId;
use Stratadox\CardGame\Match\Player\StartPlaying;
use Stratadox\CardGame\Match\Match\StartedSettingUpMatchForProposal;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposals;
use Stratadox\CommandHandling\Handler;

final class MatchStartingSaga implements EventHandler
{
    private $nextStep;
    private $proposals;
    private $numberOfCardsInOpeningHand = 7;
    private $countedCards = [];
    private $opponent = [];
    private $matchForPlayer = [];

    public function __construct(MatchProposals $proposals, Handler $nextStep)
    {
        $this->proposals = $proposals;
        $this->nextStep = $nextStep;
    }

    public function handle(DomainEvent $becauseOfWhatHappened): void
    {
        if ($becauseOfWhatHappened instanceof StartedSettingUpMatchForProposal) {
            $this->becomePlayers($becauseOfWhatHappened);
            $this->shuffleTheDecks($becauseOfWhatHappened);
        } else if ($becauseOfWhatHappened instanceof DeckHasBeenShuffled) {
            $this->prepareCards($becauseOfWhatHappened);
        } else if ($becauseOfWhatHappened instanceof AddedTheCardToTheMatch) {
            $this->prepareNextCard($becauseOfWhatHappened);
        } else if ($becauseOfWhatHappened instanceof AddedAllCardsToTheMatch) {
            $this->drawOpeningHand($becauseOfWhatHappened);
        } else if ($becauseOfWhatHappened instanceof CardWasDrawn) {
            $this->countTheCard($becauseOfWhatHappened->player());
            $this->maybeStartTheMatch(
                $becauseOfWhatHappened->player(),
                $this->matchForPlayer[(string) $becauseOfWhatHappened->player()]
            );
        }
    }

    private function becomePlayers(StartedSettingUpMatchForProposal $match): void
    {
        $proposal = $this->proposals->withId($match->proposal());

        // @todo figure out a better way to store, pass along or get rid of this state
        $this->opponent[$match->players()[0]->id()] = $match->players()[1]->id();
        $this->opponent[$match->players()[1]->id()] = $match->players()[0]->id();

        $this->matchForPlayer[$match->players()[0]->id()] = $match->aggregateId();
        $this->matchForPlayer[$match->players()[1]->id()] = $match->aggregateId();

        $this->nextStep->handle(
            StartPlaying::as($match->players()[0], $proposal->from())
        );
        $this->nextStep->handle(
            StartPlaying::as($match->players()[1], $proposal->to())
        );
    }

    private function shuffleTheDecks(StartedSettingUpMatchForProposal $match): void
    {
        foreach ($match->players() as $player) {
            $this->nextStep->handle(ShuffleDeck::for($player));
        }
    }

    private function prepareCards(DeckHasBeenShuffled $shuffled): void
    {
        $this->nextStep->handle(PrepareCard::from($shuffled->deck()));
    }

    private function prepareNextCard(AddedTheCardToTheMatch $added): void
    {
        $this->nextStep->handle(
            PrepareCard::after($added->deck(), $added->cardNumber())
        );
    }

    private function drawOpeningHand(AddedAllCardsToTheMatch $prepared): void
    {
        for ($i = 0; $i < $this->numberOfCardsInOpeningHand; $i++) {
            $this->nextStep->handle(DrawCard::from($prepared->deck()));
        }
    }

    private function countTheCard(PlayerId $forPlayer): void
    {
        if (!isset($this->countedCards[$forPlayer->id()])) {
            $this->countedCards[$forPlayer->id()] = 0;
        }
        $this->countedCards[$forPlayer->id()]++;
    }

    private function maybeStartTheMatch(PlayerId $forPlayer, MatchId $match): void
    {
        // @todo a better way
        if (
            $this->countedCards[$forPlayer->id()] === $this->numberOfCardsInOpeningHand &&
            isset($this->countedCards[$this->opponent[$forPlayer->id()]]) &&
            $this->countedCards[$this->opponent[$forPlayer->id()]] === $this->numberOfCardsInOpeningHand
        ) {
            $this->nextStep->handle(OkayLetsGo::beginThat($match));
        }
    }
}
