<?php declare(strict_types=1);

namespace Stratadox\CardGame\Account;

use Stratadox\CardGame\Command;
use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\CommandHandler;
use Stratadox\CardGame\Visiting\AllVisitors;
use function assert;

final class AccountOpeningProcess implements CommandHandler
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

    public function handle(Command $command): void
    {
        assert($command instanceof OpenAnAccount);

        $visitor = $this->visitor->withId($command->visitorId());
        if ($visitor === null) {
            $this->eventBag->add(new TriedOpeningAccountForUnknownEntity(
                $command->correlationId(),
                'Cannot open account for unknown entity'
            ));
            return;
        }

        $this->register($visitor->openAccount($this->newIdentity->generate()));
    }

    private function register(PlayerAccount $account): void
    {
        $this->playerBase->add($account);
        $this->eventBag->takeFrom($account);
    }
}
