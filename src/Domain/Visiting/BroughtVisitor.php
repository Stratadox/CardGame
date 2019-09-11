<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

final class BroughtVisitor implements RedirectSourceEvent
{
    private $source;
    private $visitor;

    public function __construct(string $source, VisitorId $visitor)
    {
        $this->source = $source;
        $this->visitor = $visitor;
    }

    public function aggregateId(): string
    {
        return $this->source;
    }

    public function visitor(): VisitorId
    {
        return $this->visitor;
    }

    public function payload(): array
    {
        return [
            'visitor' => (string) $this->visitor,
        ];
    }
}
