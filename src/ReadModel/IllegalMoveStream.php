<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel;

use function array_slice;
use function end;
use function is_string;
use Stratadox\CardGame\Match\PlayerId;

final class IllegalMoveStream
{
    private $illegalMoves = [];

    public function addFor(PlayerId $player, string $message): void
    {
        $this->illegalMoves[$player->id()][] = $message;
    }

    public function latestFor(PlayerId $player): ?string
    {
        return $this->latest($this->illegalMoves[$player->id()] ?? []);
    }

    /** @return string[] */
    public function since(int $offset, PlayerId $player): array
    {
        return array_slice($this->illegalMoves[$player->id()] ?? [], $offset);
    }

    private function latest(array $illegalMoves): ?string
    {
        if (is_string(end($illegalMoves))) {
            return end($illegalMoves);
        }
        return null;
    }
}
