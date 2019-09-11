<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use RuntimeException;
use function sprintf;
use Stratadox\CardGame\Proposal\ProposalId;

final class NoSuchMatch extends RuntimeException
{
    public static function forProposal(ProposalId $id): self
    {
        return new self(sprintf('No match found for proposal %s.', $id));
    }
}
