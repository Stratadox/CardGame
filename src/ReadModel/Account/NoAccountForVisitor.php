<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Account;

use RuntimeException;
use Stratadox\CardGame\VisitorId;
use function sprintf;

final class NoAccountForVisitor extends RuntimeException
{
    public static function withId(VisitorId $visitorId): self
    {
        return new self(sprintf(
            'No account available for the visitor with id `%s`.',
            $visitorId
        ));
    }
}
