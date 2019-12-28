<?php declare(strict_types=1);

namespace Stratadox\CardGame\Context\Step;

use function array_reduce;
use function assert;
use function implode;
use function sprintf;
use function stripos;

trait RefusalVerification
{
    /**
     * @Then that is not possible, because :reason
     */
    public function thatIsNotPossibleBecause(string $reason)
    {
        assert(
            array_reduce(
                $this->refusals(),
                static function (
                    bool $found,
                    string $refusal
                ) use ($reason): bool {
                    return $found || stripos($refusal, $reason) !== false;
                },
                false
            ), sprintf(
                'Should have found a refusal about "%s", found only [%s].',
                $reason,
                implode(', ', $this->refusals())
            )
        );
    }

    abstract protected function refusals(): array;
}
