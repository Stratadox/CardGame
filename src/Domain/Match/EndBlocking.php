<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class EndBlocking
{
    /** @var PlayerId */
    private $forThePlayer;

    public function __construct(PlayerId $forThePlayer)
    {
        $this->forThePlayer = $forThePlayer;
    }

    public static function phase(PlayerId $forWhom): self
    {
        return new self($forWhom);
    }

    public function player(): PlayerId
    {
        return $this->forThePlayer;
    }
}
