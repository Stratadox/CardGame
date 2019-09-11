<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use function assert;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Account\VisitorOpenedAnAccount;
use Stratadox\CardGame\ReadModel\PlayerList;

final class PlayerListAppender implements EventHandler
{
    private $playerList;

    public function __construct(PlayerList $playerList)
    {
        $this->playerList = $playerList;
    }

    public function handle(DomainEvent $event): void
    {
        assert($event instanceof VisitorOpenedAnAccount);

        $this->playerList->add();
    }
}
