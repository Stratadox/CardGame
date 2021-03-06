<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test;

use DateInterval;
use Hateoas\Hateoas;
use Hateoas\HateoasBuilder;
use Hateoas\Serializer\JsonHalSerializer;
use Hateoas\Serializer\XmlHalSerializer;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Xml;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Account\OpenAnAccount;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\EventHandler\AccountOverviewCreator;
use Stratadox\CardGame\EventHandler\BattlefieldUpdater;
use Stratadox\CardGame\EventHandler\BringerOfBadNews;
use Stratadox\CardGame\EventHandler\HandAdjuster;
use Stratadox\CardGame\EventHandler\MatchPublisher;
use Stratadox\CardGame\EventHandler\PlayerListAppender;
use Stratadox\CardGame\EventHandler\ProposalAcceptanceNotifier;
use Stratadox\CardGame\EventHandler\ProposalSender;
use Stratadox\CardGame\EventHandler\StatisticsUpdater;
use Stratadox\CardGame\EventHandler\TurnSwitcher;
use Stratadox\CardGame\Infrastructure\DomainEvents\Dispatcher;
use Stratadox\CardGame\Infrastructure\Rest\Serializer\LinksFirstXmlHalSerializer;
use Stratadox\CardGame\Infrastructure\Test\TestClock;
use Stratadox\CardGame\Infrastructure\Test\TestUrlGenerator;
use Stratadox\CardGame\Match\Command\StartTheMatch;
use Stratadox\CardGame\Proposal\AcceptTheProposal;
use Stratadox\CardGame\Proposal\ProposeMatch;
use Stratadox\CardGame\ReadModel\Account\AccountOverviews;
use Stratadox\CardGame\ReadModel\Match\CardTemplates;
use Stratadox\CardGame\ReadModel\Match\Battlefields;
use Stratadox\CardGame\ReadModel\Match\CardTemplate;
use Stratadox\CardGame\ReadModel\Match\CardsInHand;
use Stratadox\CardGame\ReadModel\Match\OngoingMatch;
use Stratadox\CardGame\ReadModel\Match\OngoingMatches;
use Stratadox\CardGame\ReadModel\PageVisitsStatisticsReport;
use Stratadox\CardGame\ReadModel\PlayerList;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposals;
use Stratadox\CardGame\ReadModel\Refusals;
use Stratadox\CardGame\RestInterface\Access\Greeter;
use Stratadox\CardGame\Visiting\Visit;
use Stratadox\CardGame\Visiting\VisitorId;
use Stratadox\Clock\RewindableClock;
use Stratadox\CommandHandling\Handler;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use function array_keys;
use function array_values;
use function file_get_contents;
use function sprintf;
use function str_replace;

abstract class CardGameTest extends TestCase
{
    /** @var Handler */
    private $input;

    /** @var Configuration[] */
    private $configuration = [];

    /** @var Configuration */
    private $currentConfiguration;

    /** @var Greeter */
    private $greeter;

    /** @var Hateoas */
    private $serializer;

    /** @var RewindableClock */
    protected $clock;

    /** @var PageVisitsStatisticsReport */
    protected $statistics;

    /** @var AccountOverviews */
    protected $accountOverviews;

    /** @var PlayerList */
    protected $playerList;

    /** @var MatchProposals */
    protected $matchProposals;

    /**
     * @var MatchProposals
     * @deprecated
     */
    protected $acceptedProposals;

    /** @var CardsInHand */
    protected $cardsInTheHand;

    /** @var CardTemplate[] */
    protected $testCard = [];

    /** @var OngoingMatches */
    protected $ongoingMatches;

    /** @var null|OngoingMatch */
    protected $match;

    /** @var Battlefields */
    protected $battlefield;

    /** @var Refusals */
    protected $refusals;

    /** @var CorrelationId */
    protected $id;

    /** @var int|null */
    protected $currentPlayer;

    /** @var int|null */
    protected $otherPlayer;

    protected function setUp(): void
    {
        $this->clock = TestClock::make();

        // Normally an identifier is generated for each request, allowing the
        // client to track what happened.
        // For brevity's sake, it's one id for all requests in the unit tests.
        $this->id = CorrelationId::from('foo');

        $this->statistics = new PageVisitsStatisticsReport();
        $this->matchProposals = new MatchProposals($this->clock);
        $this->acceptedProposals = $this->matchProposals;
        $this->accountOverviews = AccountOverviews::startEmpty();
        $this->playerList = PlayerList::startEmpty();
        $this->cardsInTheHand = new CardsInHand();
        $this->ongoingMatches = new OngoingMatches();
        $this->battlefield = new Battlefields();
        $this->refusals = new Refusals();
        $this->testCard = [
            new CardTemplate('card-type-1'),
            new CardTemplate('card-type-2'),
            new CardTemplate('card-type-3'),
            new CardTemplate('card-type-4'),
            new CardTemplate('card-type-5'),
            new CardTemplate('card-type-6'),
            new CardTemplate('card-type-7'),
            new CardTemplate('card-type-3'), // 3 again !
            new CardTemplate('card-type-8'),
            new CardTemplate('card-type-9'),
        ];

        $this->configuration['unit'] = UnitTestConfiguration::withClock($this->clock);
        // @todo add a test configuration where every command is delayed

        $allCards = new CardTemplates(...$this->testCard);

        $this->currentConfiguration =
            $this->configuration[$_SERVER['configuration'] ?? 'unit'];

        $this->input = $this->currentConfiguration->handler(
            new Dispatcher(
                new MatchPublisher($this->ongoingMatches, $this->matchProposals),
                new HandAdjuster($this->cardsInTheHand, $allCards),
                new BattlefieldUpdater($this->battlefield, $allCards),
                new BringerOfBadNews($this->refusals),
                new StatisticsUpdater($this->statistics),
                new PlayerListAppender($this->playerList),
                new AccountOverviewCreator($this->accountOverviews),
                new ProposalSender($this->matchProposals),
                new ProposalAcceptanceNotifier($this->matchProposals),
                new TurnSwitcher($this->ongoingMatches)
            )
        );

        $this->greeter = new Greeter();

        $serializerBuilder = new SerializerBuilder();
        $serializerBuilder
            ->addMetadataDir(__DIR__ . '/../../src/TheInfrastructure/Rest/Config')
            ->setExpressionEvaluator(new ExpressionEvaluator(
                new ExpressionLanguage(),
                ['greeter' => $this->greeter]
            ));
        $this->serializer = HateoasBuilder::create($serializerBuilder)
            ->setXmlSerializer(new LinksFirstXmlHalSerializer(new XmlHalSerializer()))
            ->setJsonSerializer(new JsonHalSerializer())
            ->addMetadataDir(__DIR__ . '/../../src/TheInfrastructure/Rest/Config')
            ->setExpressionContextVariable('greeter', $this->greeter)
            ->setUrlGenerator(null, new TestUrlGenerator('test://'))
            ->setDebug(true)
            ->build();
    }

    protected function handle(object $command): void
    {
        $this->input->handle($command);
    }

    protected function signUpForTheGame(VisitorId $visitorId): void
    {
        $this->handle(Visit::page('home', 'https://example.com', $visitorId, $this->id));
        $this->handle(OpenAnAccount::forVisitorWith($visitorId, $this->id));
    }

    protected function prepareMatchBetween(
        AccountId $playerOne,
        AccountId $playerTwo
    ): void {
        $this->handle(ProposeMatch::between($playerOne, $playerTwo, $this->id));
        $this->handle(AcceptTheProposal::withId(
            $this->matchProposals->for($playerTwo)[0]->id(),
            $playerTwo,
            $this->id
        ));
    }

    protected function setUpNewMatch(
        string $id1 = 'id-1',
        string $id2 = 'id-2'
    ): void {
        $visitor1 = VisitorId::from($id1);
        $visitor2 = VisitorId::from($id2);

        $this->signUpForTheGame($visitor1);
        $this->signUpForTheGame($visitor2);

        $this->prepareMatchBetween(
            $this->accountOverviews->forVisitor($visitor1)->id(),
            $this->accountOverviews->forVisitor($visitor2)->id()
        );

        $proposal = $this->acceptedProposals->since($this->clock->now())[0];

        $this->handle(StartTheMatch::forProposal($proposal->id(), $this->id));

        $this->match = $this->ongoingMatches->forProposal($proposal->id());
    }

    protected function determineCurrentPlayer(): void
    {
        foreach ($this->match->players() as $player) {
            if ($this->match->itIsTheTurnOf($player)) {
                $this->currentPlayer = $player;
            } else {
                $this->otherPlayer = $player;
            }
        }
    }

    protected function interval(int $seconds): DateInterval
    {
        return new DateInterval(sprintf(
            'PT%dS',
            $seconds
        ));
    }

    protected function authenticateAs(AccountId $playerAccount): void
    {
        $this->greeter->welcome($playerAccount);
    }

    protected function replace(array $replacements, string $original): string
    {
        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $original
        );
    }

    protected function fileContentsWithTagsReplaced(array $replacements, string $filename): string
    {
        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            file_get_contents($filename)
        );
    }

    protected function toJson($resource): string
    {
        return $this->serializer->serialize($resource, 'json');
    }

    protected function toXml($resource): string
    {
        return $this->serializer->serialize($resource, 'xml');
    }

    protected function assertXmlStringIsAcceptedByXsdFile(
        string $xsd,
        string $xml
    ): void {
        $this->assertThat(Xml::load($xml), IsAcceptedByXsd::file($xsd));
    }

    protected function assertJsonStringIsAcceptedByJsonSchemaFile(
        string $schema,
        string $json
    ): void {
        $this->assertThat($json, IsAcceptedByJsonSchema::file($schema));
    }
}
