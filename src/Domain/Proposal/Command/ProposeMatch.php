<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal\Command;

use Stratadox\CardGame\AccountId;

final class ProposeMatch
{
    private $proposedBy;
    private $proposedTo;

    private function __construct(
        AccountId $proposedBy,
        AccountId $proposedTo
    ) {
        $this->proposedBy = $proposedBy;
        $this->proposedTo = $proposedTo;
    }

    public static function between(
        AccountId $proposedBy,
        AccountId $proposedTo
    ): ProposeMatch {
        return new self($proposedBy, $proposedTo);
    }

    public function proposedBy(): AccountId
    {
        return $this->proposedBy;
    }

    public function proposedTo(): AccountId
    {
        return $this->proposedTo;
    }
}
