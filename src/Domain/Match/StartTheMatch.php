<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\Proposal\ProposalId;

final class StartTheMatch
{
    private $proposal;

    public function __construct(ProposalId $proposal)
    {
        $this->proposal = $proposal;
    }

    public static function forProposal(ProposalId $proposal): self
    {
        return new self($proposal);
    }

    public function proposal(): ProposalId
    {
        return $this->proposal;
    }
}