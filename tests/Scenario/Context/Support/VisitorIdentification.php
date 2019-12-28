<?php declare(strict_types=1);

namespace Stratadox\CardGame\Context\Support;

use Stratadox\CardGame\Visiting\VisitorId;

trait VisitorIdentification
{
    /** @var VisitorId[] */
    private $visitorIdFor = [];

    protected function setVisitor(string $player, VisitorId $id): void
    {
        $this->visitorIdFor[$player] = $id;
    }

    protected function visitor(string $player): VisitorId
    {
        return $this->visitorIdFor[$player];
    }
}
