<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use RuntimeException;

final class ProposalHasAlreadyExpired extends RuntimeException
{
    public static function cannotAcceptItAnymore(): self
    {
        return new self(
            'The proposal has already expired!'
        );
    }
}
