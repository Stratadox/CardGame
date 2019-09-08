<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Stratadox\Clock\RewindableClock;
use Stratadox\Clock\RewindableDateTimeClock;
use Stratadox\Clock\UnmovingClock;

final class TestClock implements RewindableClock
{
    private $clock;

    public function __construct(RewindableClock $clock)
    {
        $this->clock = $clock;
    }

    public static function make(): self
    {
        return new self(RewindableDateTimeClock::using(
            UnmovingClock::standingStillAt(new DateTimeImmutable())
        ));
    }

    public function now(): DateTimeInterface
    {
        return $this->clock->now();
    }

    public function rewind(DateInterval $interval): RewindableClock
    {
        $this->clock = $this->clock->rewind($interval);
        return $this;
    }

    public function fastForward(DateInterval $interval): RewindableClock
    {
        $this->clock = $this->clock->fastForward($interval);
        return $this;
    }
}
