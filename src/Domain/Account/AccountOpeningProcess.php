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
            $this->eventBag->add(
                new TriedOpeningAccountForUnknownEntity($command->correlationId())
            );
            return;
        }

        $this->register($visitor->openAccount($this->newIdentity->generate()));
    }

    private function register(PlayerAccount $theAccount): void
    {
        $this->playerBase->add($theAccount);
        $this->eventBag->takeFrom($theAccount);
    }
}
