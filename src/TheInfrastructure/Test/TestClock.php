<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Closure;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Stratadox\Clock\RewindableClock;
use Stratadox\Clock\RewindableDateTimeClock;
use Stratadox\Clock\UnmovingClock;

final class TestClock implements RewindableClock
{
    /** @var RewindableClock */
    private $clock;
    /** @var Closure|null */
    private $method;

    private function __construct(RewindableClock $clock)
    {
        $this->clock = $clock;
    }

    public static function make(): self
    {
        return new self(RewindableDateTimeClock::using(
            UnmovingClock::standingStillAt(new DateTimeImmutable())
        ));
    }

    public function eachPassingSecondApply(Closure $method): void
    {
        $this->method = $method;
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
        $before = $this->clock->now();
        $this->clock = $this->clock->fastForward($interval);

        $since = $this->clock->now()->getTimestamp() - $before->getTimestamp();
        if ($this->method !== null) {
            for ($i = 0; $i < $since; $i++) {
                ($this->method)($i);
            }
        }
        return $this;
    }
}
