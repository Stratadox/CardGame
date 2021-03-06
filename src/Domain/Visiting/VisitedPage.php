<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

final class VisitedPage implements VisitorEvent
{
    private $visitor;
    private $page;
    private $source;
    private $firstVisit;

    public function __construct(
        VisitorId $visitor,
        string $page,
        string $redirectSource,
        bool $firstVisit
    ) {
        $this->visitor = $visitor;
        $this->page = $page;
        $this->source = $redirectSource;
        $this->firstVisit = $firstVisit;
    }

    public function aggregateId(): VisitorId
    {
        return $this->visitor;
    }

    public function page(): string
    {
        return $this->page;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function isFirstVisit(): bool
    {
        return $this->firstVisit;
    }
}
