<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CardGame\Visiting\AllVisitors;
use Stratadox\CardGame\Visiting\Visitor;
use Stratadox\CardGame\VisitorId;

final class InMemoryVisitorRepository implements AllVisitors
{
    private $visitors = [];

    public function add(Visitor $visitor): void
    {
        $this->visitors[(string) $visitor->id()] = $visitor;
    }

    public function withId(VisitorId $id): ?Visitor
    {
        return $this->visitors[(string) $id] ?? null;
    }
}
