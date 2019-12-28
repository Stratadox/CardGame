<?php declare(strict_types=1);

namespace Stratadox\CardGame\Context\Support;

use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\ReadModel\Account\AccountOverviews;
use Stratadox\CardGame\Visiting\VisitorId;
use function assert;

trait Accounts
{
    /** @var AccountOverviews */
    private $accountOverviews;

    protected function account(string $player): AccountId
    {
        return $this->accountFor($this->visitor($player));
    }

    private function accountFor(VisitorId $visitor): AccountId
    {
        assert($this->accountOverviews !== null);
        return $this->accountOverviews->forVisitor($visitor)->id();
    }

    abstract protected function visitor(string $player): VisitorId;
}
