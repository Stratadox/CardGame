<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Proposal;

use DateTimeInterface;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Proposal\ProposalId;
use Stratadox\Clock\Clock;
use function array_filter;
use function array_values;

class MatchProposals
{
    /** @var MatchProposal[] */
    private $proposals = [];
    /** @var Clock */
    private $clock;

    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    public function add(MatchProposal $proposal): void
    {
        $this->proposals[(string) $proposal->id()] = $proposal;
    }

    /** @return MatchProposal[] */
    public function for(AccountId $player, DateTimeInterface $currently = null): array
    {
        $currently = $currently ?: $this->clock->now();
        $proposals = [];
        foreach ($this->proposals as $proposal) {
            if ($proposal->canBeAcceptedBy($player, $currently)) {
                $proposals[] = $proposal;
            }
        }
        return $proposals;
    }

    /**
     * @return MatchProposal[]
     * @deprecated use acceptedSince
     */
    public function since(DateTimeInterface $begin): array
    {
        return $this->acceptedSince($begin);
    }

    /** @return MatchProposal[] */
    public function acceptedSince(DateTimeInterface $when): array
    {
        // @todo filter by date
        return array_values(array_filter(
            $this->proposals,
            static function (MatchProposal $proposal): bool {
                return $proposal->hasBeenAccepted();
            }
        ));
    }

    /**
     * @deprecated
     * @see acceptedFrom
     */
    public function proposedBy(
        AccountId $account,
        DateTimeInterface $since
    ): array {
        return $this->acceptedFrom($account, $since);
    }

    /** @return MatchProposal[] */
    public function acceptedFrom(
        AccountId $account,
        DateTimeInterface $since
    ): array {
        return array_filter(
            $this->acceptedSince($since),
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
            $this->acceptedSince($since),
            static function (MatchProposal $proposal) use ($account): bool {
                return $proposal->wasProposedTo($account);
            }
        );
    }

    public function byId(ProposalId $id): MatchProposal
    {
        return $this->proposals[(string) $id];
    }
}
