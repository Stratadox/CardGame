<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

use function assert;
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

    public function bring(VisitorId $visitor): void
    {
        assert(!isset($this->redirectedVisitors[$visitor->id()]));

        $this->redirectedVisitors[$visitor->id()] = $visitor;
        $this->events[] = new BroughtVisitor($this->id);
    }

    public function visitorWithId(VisitorId $visitorId): Visitor
    {
        return new Visitor($visitorId);
    }
}
