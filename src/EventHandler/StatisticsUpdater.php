<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use function assert;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\ReadModel\PageVisitsStatisticsReport;
use Stratadox\CardGame\Visiting\Event\BroughtVisitor;
use Stratadox\CardGame\Visiting\Event\VisitedPage;

final class StatisticsUpdater implements EventHandler
{
    private $statistics;

    public function __construct(PageVisitsStatisticsReport $statistics)
    {
        $this->statistics = $statistics;
    }

    public function handle(DomainEvent $event): void
    {
        if ($event instanceof BroughtVisitor) {
            $this->newVisitor($event);
        } else {
            assert($event instanceof VisitedPage);
            $this->recurringVisitor($event);
        }
    }

    private function newVisitor(BroughtVisitor $event): void
    {
        $this->statistics->firstVisitFrom($event->aggregateId());
    }

    private function recurringVisitor(VisitedPage $event): void
    {
        if ($event->isFirstVisit()) {
            $this->statistics->firstVisitTo($event->page());
        }
        $this->statistics->visit($event->page(), $event->source());
    }
}
