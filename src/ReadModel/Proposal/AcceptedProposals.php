<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Proposal;

use DateTimeInterface;
use Stratadox\CardGame\Account\AccountId;
use function array_filter;

class AcceptedProposals
{
    private $proposals = [];

    public function add(MatchProposal $proposal): void
    {
        $this->proposals[] = $proposal;
    }

    /** @return MatchProposal[] */
    public function since(DateTimeInterface $begin): array
    {
        // @todo
        return $this->proposals;
    }

    /** @return MatchProposal[] */
    public function proposedBy(
        AccountId $account,
        DateTimeInterface $since
    ): array {
        return array_filter(
            $this->since($since),
            static function (MatchProposal $proposal) use ($account): bool {
                return $proposal->wasProposedBy($account);
            }
        );
    }

    /** @return MatchProposal[] */
    public function acceptedBy(
        AccountId $account,
        DateTimeInterface $since
    ): array {
        return array_filter(
            $this->since($since),
            static function (MatchProposal $proposal) use ($account): bool {
                return $proposal->wasProposedTo($account);
            }
        );
    }
}
