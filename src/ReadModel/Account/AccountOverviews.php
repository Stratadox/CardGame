<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Account;

use Stratadox\CardGame\VisitorId;

class AccountOverviews
{
    /** @var AccountOverview[] */
    private $accounts = [];

    public function add(AccountOverview $accountOverview): void
    {
        $this->accounts[] = $accountOverview;
    }

    /** @throws NoAccountForVisitor */
    public function forVisitor(VisitorId $theVisitor): AccountOverview
    {
        foreach ($this->accounts as $theAccountOverview) {
            if ($theVisitor->is($theAccountOverview->visitor())) {
                return $theAccountOverview;
            }
        }
        throw NoAccountForVisitor::withId($theVisitor);
    }
}
