<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel;

use function array_slice;
use function end;
use function is_string;
use Stratadox\CardGame\Match\MatchId;

final class IllegalMoveStream
{
    private $illegalMoves = [];

    public function addFor(MatchId $match, int $player, string $message): void
    {
        $this->illegalMoves[$match->id()][$player][] = $message;
    }

    public function latestFor(MatchId $match, int $player): ?string
    {
        return $this->latest($this->illegalMoves[$match->id()][$player] ?? []);
    }

    /** @return string[] */
    public function since(int $offset, MatchId $match, int $player): array
    {
        return array_slice($this->illegalMoves[$match->id()][$player] ?? [], $offset);
    }

    private function latest(array $illegalMoves): ?string
    {
        if (is_string(end($illegalMoves))) {
            return end($illegalMoves);
        }
        return null;
    }
}
