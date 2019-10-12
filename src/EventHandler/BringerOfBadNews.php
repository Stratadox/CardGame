<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use function array_keys;
use function assert;
use function get_class;
use Stratadox\CardGame\Account\TriedOpeningAccountForUnknownEntity;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\PlayerDidNotHaveTheMana;
use Stratadox\CardGame\Match\Event\TriedAttackingWithUnknownCard;
use Stratadox\CardGame\Match\Event\TriedBlockingOutOfTurn;
use Stratadox\CardGame\Match\Event\TriedPlayingCardOutOfTurn;
use Stratadox\CardGame\Match\Event\TriedStartingMatchForPendingProposal;
use Stratadox\CardGame\Match\Event\TriedStartingMatchWithoutProposal;
use Stratadox\CardGame\Proposal\TriedAcceptingExpiredProposal;
use Stratadox\CardGame\Proposal\TriedAcceptingUnknownProposal;
use Stratadox\CardGame\ReadModel\Refusals;
use Stratadox\CardGame\RefusalEvent;

final class BringerOfBadNews implements EventHandler
{
    private $refusals;
    private static $messages = [
        TriedOpeningAccountForUnknownEntity::class =>
            'Cannot open account for unknown entity',
        TriedStartingMatchForPendingProposal::class =>
            'The proposal is still pending!',
        TriedAcceptingExpiredProposal::class => 'The proposal has already expired!',
        TriedAcceptingUnknownProposal::class => 'Proposal not found',
        TriedStartingMatchWithoutProposal::class => 'Proposal not found',
        PlayerDidNotHaveTheMana::class => 'Not enough mana!',
        TriedPlayingCardOutOfTurn::class => 'Cannot play cards right now',
        TriedAttackingWithUnknownCard::class => 'That card does not exist',
        TriedBlockingOutOfTurn::class => 'Cannot block at this time',
    ];

    public function __construct(Refusals $refusals)
    {
        $this->refusals = $refusals;
    }

    public function events(): iterable
    {
        return array_keys(self::$messages);
    }

    public function handle(DomainEvent $event): void
    {
        assert($event instanceof RefusalEvent);

        $this->refusals->addFor(
            $event->aggregateId(),
            BringerOfBadNews::$messages[get_class($event)]
        );
    }
}
