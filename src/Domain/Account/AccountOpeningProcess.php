<?php declare(strict_types=1);

namespace Stratadox\CardGame\Account;

use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Visiting\AllVisitors;
use Stratadox\CommandHandling\Handler;
use function assert;

final class AccountOpeningProcess implements Handler
{
    private $newIdentity;
    private $visitor;
    private $playerBase;
    private $eventBag;

    public function __construct(
        AccountIdGenerator $identityGenerator,
        AllVisitors $visitors,
        PlayerBase $playerBase,
        EventBag $eventBag
    ) {
        $this->newIdentity = $identityGenerator;
        $this->visitor = $visitors;
        $this->playerBase = $playerBase;
        $this->eventBag = $eventBag;
    }

    public function handle(object $command): void
    {
        assert($command instanceof OpenAnAccount);

        $visitor = $this->visitor->withId($command->visitorId());
        if ($visitor === null) {
            return;
        }

        $player = $visitor->openAccount($this->newIdentity->generate());
        $this->playerBase->add($player);
        $this->eventBag->takeFrom($player);
    }
}
