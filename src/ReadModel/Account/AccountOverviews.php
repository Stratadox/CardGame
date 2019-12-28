<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Account;

use Stratadox\CardGame\Visiting\VisitorId;

class AccountOverviews
{
    /** @var AccountOverview[] */
    private $accounts = [];

    public static function startEmpty(): self
    {
        return new self();
    }

    public function add(AccountOverview $accountOverview): void
    {
        $this->accounts[] = $accountOverview;
    }

    /** @throws NoAccountForVisitor */
    public function forVisitor(VisitorId $visitor): AccountOverview
    {
        foreach ($this->accounts as $accountOverview) {
            if ($visitor->is($accountOverview->visitor())) {
                return $accountOverview;
            }
        }
        throw NoAccountForVisitor::withId($visitor);
    }
}
