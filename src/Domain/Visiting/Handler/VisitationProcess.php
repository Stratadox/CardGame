<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting\Handler;

use function assert;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Visiting\AllVisitors;
use Stratadox\CardGame\Visiting\Command\Visit;
use Stratadox\CardGame\Visiting\RedirectSources;
use Stratadox\Clock\Clock;
use Stratadox\CommandHandling\Handler;

final class VisitationProcess implements Handler
{
    private $allVisitors;
    private $redirectSources;
    private $clock;
    private $eventBag;

    public function __construct(
        AllVisitors $allVisitors,
        RedirectSources $redirectSources,
        Clock $clock,
        EventBag $eventBag
    ) {
        $this->allVisitors = $allVisitors;
        $this->redirectSources = $redirectSources;
        $this->clock = $clock;
        $this->eventBag = $eventBag;
    }

    public function handle(object $visit): void
    {
        assert($visit instanceof Visit);

        $source = $this->redirectSources->named($visit->redirectSource());
        $visitorId = $visit->visitorId();
        $visitor = $this->allVisitors->withId($visitorId);

        if ($visitor === null) {
            $source->bring($visitorId);
            $visitor = $source->visitorWithId($visitorId);
            $this->allVisitors->add($visitor);
        }

        $visitor->visit($visit->whichPage(), $this->clock->now(), $source->id());

        $this->eventBag->add(...$source->domainEvents());
        $source->eraseEvents();
        $this->eventBag->add(...$visitor->domainEvents());
        $visitor->eraseEvents();
    }
}
