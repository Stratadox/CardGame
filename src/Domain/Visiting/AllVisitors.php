<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

use Stratadox\CardGame\VisitorId;

interface AllVisitors
{
    public function add(Visitor $visitor): void ;
    public function withId(VisitorId $id): ?Visitor;
}
