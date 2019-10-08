<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel;

use function array_slice;
use function end;
use function is_string;
use Stratadox\CardGame\Proposal\ProposalId;

final class ProposalProblemStream
{
    private $illegalMoves = [];

    public function addFor(ProposalId $proposal, string $message): void
    {
        $this->illegalMoves[$proposal->id()][] = $message;
    }

    public function latestFor(ProposalId $proposal): ?string
    {
        return $this->latest($this->illegalMoves[$proposal->id()] ?? []);
    }

    /** @return string[] */
    public function since(int $offset, ProposalId $proposal): array
    {
        return array_slice($this->illegalMoves[$proposal->id()] ?? [], $offset);
    }

    private function latest(array $illegalMoves): ?string
    {
        return is_string(end($illegalMoves)) ? end($illegalMoves) : null;
    }
}
