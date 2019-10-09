<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel;

use Countable;

final class PlayerList implements Countable
{
    private $empty = true;

    public static function startEmpty(): self
    {
        return new self();
    }

    public function add(): void
    {
        $this->empty = false;
    }

    public function count(): int
    {
        // @todo make it an actual list
        return (int) !$this->empty;
    }
}
