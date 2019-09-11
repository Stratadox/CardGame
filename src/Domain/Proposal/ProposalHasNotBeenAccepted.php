<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use RuntimeException;
use function sprintf;

final class ProposalHasNotBeenAccepted extends RuntimeException
{
    public static function cannotStartMatch(ProposalId $proposal): self
    {
        return new self(sprintf(
            'Cannot start the match, because proposal %s has not been accepted.',
            $proposal
        ));
    }
}

