<?php declare(strict_types=1);

namespace Stratadox\CardGame\Context;

use Behat\Behat\Context\Context;
use DateInterval;
use DateTimeInterface;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\Context\Step\ProposalAccepting;
use Stratadox\CardGame\Context\Step\RefusalVerification;
use Stratadox\CardGame\Context\Step\SigningUp;
use Stratadox\CardGame\Context\Support\Accounts;
use Stratadox\CardGame\Context\Support\Correlation;
use Stratadox\CardGame\Context\Support\ProposalTracking;
use Stratadox\CardGame\Context\Support\VisitorIdentification;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\EventHandler\AccountOverviewCreator;
use Stratadox\CardGame\EventHandler\BringerOfBadNews;
use Stratadox\CardGame\EventHandler\ProposalAcceptanceNotifier;
use Stratadox\CardGame\EventHandler\ProposalSender;
use Stratadox\CardGame\Infrastructure\DomainEvents\Dispatcher;
use Stratadox\CardGame\Infrastructure\Test\TestClock;
use Stratadox\CardGame\ReadModel\Account\AccountOverviews;
use Stratadox\CardGame\ReadModel\Proposal\AcceptedProposals;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposals;
use Stratadox\CardGame\ReadModel\Refusals;
use Stratadox\CardGame\Test\UnitTestConfiguration;
use Stratadox\CommandHandling\Handler;
use function assert;
use function count;

final class ProposalUnitContext implements Context
{
    use RefusalVerification, SigningUp, ProposalAccepting,
        VisitorIdentification, Accounts, Correlation, ProposalTracking;

    /** @var DateTimeInterface */
    private $beginning;
    /** @var CorrelationId */
    private $correlation;
    /** @var Handler */
    private $input;
    /** @var Refusals */
    private $refusals;
    /** @var TestClock */
    private $clock;
    /** @var AcceptedProposals */
    private $acceptedProposals;

    /**
     * @BeforeScenario
     */
    public function setUp(): void
    {
        $this->clock = TestClock::make();
        $this->beginning = $this->clock->now();
        $this->correlation = CorrelationId::from('some-correlation-id');
        $this->refusals = new Refusals();
        $this->matchProposals = new MatchProposals($this->clock);
        $this->acceptedProposals = new AcceptedProposals();
        $this->accountOverviews = AccountOverviews::startEmpty();
        $this->input = UnitTestConfiguration::withClock($this->clock)->handler(
            new Dispatcher(
                new BringerOfBadNews($this->refusals),
                new AccountOverviewCreator($this->accountOverviews),
                new ProposalSender($this->matchProposals),
                new ProposalAcceptanceNotifier(
                    $this->acceptedProposals,
                    $this->matchProposals
                )
            )
        );
    }

    /**
     * @When no proposals are made
     */
    public function nothingToSeeHere()
    {
    }

    /**
     * @Then :player will have :amount open match proposal
     * @Then :player will have :amount open match proposals
     */
    public function willHaveThisManyOpenMatchProposals(string $player, int $amount)
    {
        $proposals = count($this->matchProposals->for($this->account($player)));
        assert(
            $proposals === $amount,
            "$player should have $amount open proposals, $proposals found."
        );
    }

    /**
     * @Then :player will have :amount of their proposals accepted
     */
    public function willHaveProposalsAccepted(string $player, int $amount)
    {
        $accepted = count($this->acceptedProposals->proposedBy(
            $this->account($player),
            $this->beginning
        ));
        assert(
            $accepted === $amount,
            "$player should see $amount of their proposals accepted, $accepted found."
        );
    }

    /**
     * @Then :player will have accepted :amount proposals
     * @Then :player will have accepted :amount proposal
     */
    public function willHaveAcceptedProposals(string $player, int $amount)
    {
        $accepted = count($this->acceptedProposals->acceptedBy(
            $this->account($player),
            $this->beginning
        ));
        assert(
            $accepted === $amount,
            "$player should see they accepted $amount proposals, $accepted found."
        );
    }

    /**
     * @Given the proposal has expired
     * @When the proposal expires
     */
    public function theProposalExpires()
    {
        $this->clock->fastForward(new DateInterval('PT1H'));
    }

    /**
     * @Given the proposal has almost expired
     */
    public function theProposalHasAlmostExpired()
    {
        $this->clock->fastForward(new DateInterval('PT30S'));
    }

    protected function refusals(): array
    {
        return $this->refusals->for($this->correlation());
    }

    protected function handle(Command $command): void
    {
        $this->input->handle($command);
    }
}
