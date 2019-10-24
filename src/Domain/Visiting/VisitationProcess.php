<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

use function assert;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\CommandHandler;
use Stratadox\Clock\Clock;

final class VisitationProcess implements CommandHandler
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

    public function handle(Command $visit): void
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

        $this->eventBag->takeFrom($source);
        $this->eventBag->takeFrom($visitor);
    }
}
