<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CardGame\Account\PlayerAccount;
use Stratadox\CardGame\Account\PlayerBase;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\ReadModel\Account\NoAccountForVisitor;

/**
 * In-memory repository for AccountOverviews; read models used for retrieving
 * information about accounts.
 */
final class InMemoryPlayerBase implements PlayerBase
{
    private $overviews;

    public function add(PlayerAccount $player): void
    {
        $this->overviews[(string) $player->id()] = $player;
    }

    public function withId(AccountId $id): PlayerAccount
    {
        return $this->overviews[(string) $id];
    }
}
