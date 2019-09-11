<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use function assert;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Account\VisitorOpenedAnAccount;
use Stratadox\CardGame\ReadModel\Account\AccountOverview;
use Stratadox\CardGame\ReadModel\Account\AccountOverviews;

final class AccountOverviewCreator implements EventHandler
{
    private $accountOverviews;

    public function __construct(AccountOverviews $accountOverviews)
    {
        $this->accountOverviews = $accountOverviews;
    }

    public function handle(DomainEvent $event): void
    {
        assert($event instanceof VisitorOpenedAnAccount);

        $this->accountOverviews->add(new AccountOverview(
            $event->aggregateId(),
            $event->forVisitor()
        ));
    }
}
