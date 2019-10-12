<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use RuntimeException;
use function sprintf;

final class ProposalHasNotBeenAccepted extends RuntimeException
{
    public static function cannotStartMatch(): self
    {
        return new self('The proposal is still pending!');
    }
}

