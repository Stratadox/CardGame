<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;

final class RedirectSource implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $redirectedVisitors = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function bring(VisitorId $visitorId): void
    {
        if (isset($this->redirectedVisitors[(string) $visitorId])) {
            return;
        }
        $visitor = new Visitor($visitorId);

        $this->redirectedVisitors[(string) $visitorId] = $visitor;
        $this->events[] = new BroughtVisitor($this->id, $visitorId);
    }

    public function visitorWithId(VisitorId $visitorId): Visitor
    {
        if (isset($this->redirectedVisitors[(string) $visitorId])) {
            return $this->redirectedVisitors[(string) $visitorId];
        }
        // @todo throw
    }
}
