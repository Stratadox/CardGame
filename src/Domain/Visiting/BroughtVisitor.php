<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

final class BroughtVisitor implements RedirectSourceEvent
{
    private $source;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function aggregateId(): string
    {
        return $this->source;
    }
}
