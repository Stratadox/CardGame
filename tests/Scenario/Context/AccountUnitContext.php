<?php declare(strict_types=1);

namespace Stratadox\CardGame\Context;

use Behat\Behat\Context\Context;
use Stratadox\CardGame\Account\OpenAnAccount;
use Stratadox\CardGame\Context\Step\RefusalVerification;
use Stratadox\CardGame\Context\Support\Correlation;
use Stratadox\CardGame\EventHandler\AccountOverviewCreator;
use Stratadox\CardGame\EventHandler\BringerOfBadNews;
use Stratadox\CardGame\EventHandler\PlayerListAppender;
use Stratadox\CardGame\Infrastructure\DomainEvents\Dispatcher;
use Stratadox\CardGame\ReadModel\Account\AccountOverviews;
use Stratadox\CardGame\ReadModel\Account\NoAccountForVisitor;
use Stratadox\CardGame\ReadModel\PlayerList;
use Stratadox\CardGame\ReadModel\Refusals;
use Stratadox\CardGame\Test\UnitTestConfiguration;
use Stratadox\CardGame\Visiting\Visit;
use Stratadox\CardGame\Visiting\VisitorId;
use Stratadox\CommandHandling\Handler;
use function assert;
use function count;
use function strpos;

final class AccountUnitContext implements Context
{
    use RefusalVerification, Correlation;

    /** @var Handler */
    private $input;
    /** @var VisitorId */
    private $visitor;
    /** @var AccountOverviews */
    private $accountOverviews;
    /** @var PlayerList */
    private $playerList;
    /** @var Refusals */
    protected $refusals;

    /**
     * @BeforeScenario
     */
    public function setUp(): void
    {
        $this->visitor = VisitorId::from('some-visitor-id');
        $this->accountOverviews = AccountOverviews::startEmpty();
        $this->playerList = PlayerList::startEmpty();
        $this->refusals = new Refusals();
        $this->input = UnitTestConfiguration::make()->handler(
            new Dispatcher(
                new BringerOfBadNews($this->refusals),
                new PlayerListAppender($this->playerList),
                new AccountOverviewCreator($this->accountOverviews)
            )
        );
    }

    /**
     * @Given I visited the :which page
     * @When I visit the :which page
     */
    public function iVisitedThePage(string $which)
    {
        $this->input->handle(
            Visit::page($which, 'source', $this->visitor, $this->correlation())
        );
    }

    /**
     * @When I open an account
     */
    public function iOpenAnAccount()
    {
        $this->input->handle(
            OpenAnAccount::forVisitorWith($this->visitor, $this->correlation())
        );
    }

    /**
     * @Then my account will be a guest account
     */
    public function myAccountWillBeAGuestAccount()
    {
        $account = $this->accountOverviews->forVisitor($this->visitor);

        assert($account->isGuestAccount());
    }

    /**
     * @Then I will not have an account
     */
    public function iWillNotHaveAnAccount()
    {
        try {
            $this->accountOverviews->forVisitor($this->visitor);
            assert(!'Should not find an account for the visitor');
        } catch (NoAccountForVisitor $e) {
            assert(strpos($e->getMessage(), $this->visitor->id()) !== false);
        }
    }

    /**
     * @Then the player list will be empty
     */
    public function thePlayerListWillBeEmpty()
    {
        assert(count($this->playerList) === 0);
    }

    /**
     * @Then the player list will not be empty
     */
    public function thePlayerListWillNotBeEmpty()
    {
        assert(count($this->playerList) !== 0);
    }

    protected function refusals(): array
    {
        return $this->refusals->for($this->correlation());
    }
}
