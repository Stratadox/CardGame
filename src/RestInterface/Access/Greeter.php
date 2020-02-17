<?php declare(strict_types=1);

namespace Stratadox\CardGame\RestInterface\Access;

use Stratadox\CardGame\Account\AccountId;

class Greeter
{
    private $account;

    public function welcome(AccountId $playerAccount): void
    {
        $this->account = $playerAccount;
    }

    /** @throws NobodyToAnnounce */
    public function announce(): AccountId
    {
        if (!$this->account) {
            throw NobodyToAnnounce::atThisPoint();
        }
        return $this->account;
    }
}
