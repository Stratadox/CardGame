<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

use DateTimeInterface;

final class VisitedPage implements VisitorEvent
{
    private $visitor;
    private $page;
    private $when;
    private $source;
    private $firstVisit;

    public function __construct(
        VisitorId $visitor,
        string $page,
        DateTimeInterface $when,
        string $redirectSource,
        bool $firstVisit
    ) {
        $this->visitor = $visitor;
        $this->page = $page;
        $this->when = $when;
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

    public function when(): DateTimeInterface
    {
        return $this->when;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function isFirstVisit(): bool
    {
        return $this->firstVisit;
    }

    public function payload(): array
    {
        return [
            'page' => $this->page(),
            'when' => $this->when(),
            'source' => $this->source(),
            'firstVisit' => $this->isFirstVisit(),
        ];
    }
}
