<?php declare(strict_types=1);

namespace Stratadox\CardGame\Account;

interface PlayerBase
{
    public function add(PlayerAccount $thePlayer): void;
    public function withId(AccountId $id): PlayerAccount;
}
