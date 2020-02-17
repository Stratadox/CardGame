<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use DateInterval;
use Stratadox\CardGame\Match\Command\StartTheMatch;
use Stratadox\CardGame\Proposal\AcceptTheProposal;
use Stratadox\CardGame\Proposal\ProposalId;
use Stratadox\CardGame\Proposal\ProposeMatch;
use Stratadox\CardGame\Test\CardGameTest;
use Stratadox\CardGame\Visiting\VisitorId;

/**
 * @testdox viewing the accepted match match proposal resource
 */
class viewing_the_accepted_match_proposal_resource extends CardGameTest
{
    private $account;
    private $otherAccount;
    private $allBegan;
    private $expire;

    protected function setUp(): void
    {
        $this->markTestSkipped('@todo');

        parent::setUp();

        $visitor1 = VisitorId::from('id-1');
        $visitor2 = VisitorId::from('id-2');
        $this->signUpForTheGame($visitor1);
        $this->signUpForTheGame($visitor2);

        $this->account = $this->accountOverviews
            ->forVisitor($visitor1)
            ->id();
        $this->otherAccount = $this->accountOverviews
            ->forVisitor($visitor2)
            ->id();

        $this->allBegan = $this->clock->now();
        $this->expire = $this->allBegan->add(
            DateInterval::createFromDateString('30 seconds')
        );
        $this->authenticateAs($this->account);
    }

    /** @test */
    function viewing_an_empty_list_of_the_accepted_proposals_as_json()
    {
        $this->assertJsonStringEqualsJsonString(
            $this->fileContentsWithTagsReplaced(
                ['(ACCOUNT)' => $this->account],
                __DIR__ . '/../../../schema/proposals/v1/example/accepted/empty.json'
            ),
            $this->toJson($this->matchProposals->acceptedBy($this->account, $this->allBegan))
        );
    }

    /** @test */
    function viewing_an_empty_list_of_the_accepted_proposals_as_xml()
    {
        $this->assertXmlStringEqualsXmlString(
            $this->fileContentsWithTagsReplaced(
                ['(ACCOUNT)' => $this->account],
                __DIR__ . '/../../../schema/proposals/v1/example/accepted/empty.xml'
            ),
            $this->toXml($this->matchProposals->acceptedBy($this->account, $this->allBegan))
        );
    }

    /** @test */
    function viewing_a_list_with_an_accepted_proposal_as_json()
    {
        $proposalId = $this->proposeAndAccept();

        $this->assertJsonStringEqualsJsonString(
            $this->fileContentsWithTagsReplaced(
                [
                    '(ACCOUNT)' => $this->account,
                    '(OTHER-ACCOUNT)' => $this->otherAccount,
                    '(PROPOSAL)' => $proposalId,
                    '(EXPIRE)' => $this->expire->format('c'),
                ],
                __DIR__ . '/../../../schema/proposals/v1/example/accepted/pending.json'
            ),
            $this->toJson($this->matchProposals->acceptedBy($this->account, $this->allBegan))
        );
    }

    /** @test */
    function viewing_a_list_with_an_accepted_proposal_as_xml()
    {
        $proposalId = $this->proposeAndAccept();

        $this->assertXmlStringEqualsXmlString(
            $this->fileContentsWithTagsReplaced(
                [
                    '(ACCOUNT)' => $this->account,
                    '(OTHER-ACCOUNT)' => $this->otherAccount,
                    '(PROPOSAL)' => $proposalId,
                    '(EXPIRE)' => $this->expire->format('c'),
                ],
                __DIR__ . '/../../../schema/proposals/v1/example/accepted/pending.xml'
            ),
            $this->toXml($this->matchProposals->acceptedBy($this->account, $this->allBegan))
        );
    }

    /** @test */
    function viewing_a_list_with_a_started_proposal_as_json()
    {
        $proposalId = $this->proposeAndAccept();
        $this->handle(StartTheMatch::forProposal($proposalId, $this->id));
        $matchId = $this->ongoingMatches->forProposal($proposalId)->id();

        $this->assertJsonStringEqualsJsonString(
            $this->fileContentsWithTagsReplaced(
                [
                    '(ACCOUNT)' => $this->account,
                    '(OTHER-ACCOUNT)' => $this->otherAccount,
                    '(PROPOSAL)' => $proposalId,
                    '(MATCH)' => $matchId,
                    '(EXPIRE)' => $this->expire->format('c'),
                ],
                __DIR__ . '/../../../schema/proposals/v1/example/accepted/started.json'
            ),
            $this->toJson($this->matchProposals->acceptedBy($this->account, $this->allBegan))
        );
    }

    /** @test */
    function viewing_a_list_with_a_started_proposal_as_xml()
    {
        $proposalId = $this->proposeAndAccept();
        $this->handle(StartTheMatch::forProposal($proposalId, $this->id));
        $matchId = $this->ongoingMatches->forProposal($proposalId)->id();

        $this->assertXmlStringEqualsXmlString(
            $this->fileContentsWithTagsReplaced(
                [
                    '(ACCOUNT)' => $this->account,
                    '(OTHER-ACCOUNT)' => $this->otherAccount,
                    '(PROPOSAL)' => $proposalId,
                    '(MATCH)' => $matchId,
                    '(EXPIRE)' => $this->expire->format('c'),
                ],
                __DIR__ . '/../../../schema/proposals/v1/example/accepted/started.xml'
            ),
            $this->toXml($this->matchProposals->acceptedBy($this->account, $this->allBegan))
        );
    }

    private function proposeAndAccept(): ProposalId
    {
        $this->handle(ProposeMatch::between(
            $this->otherAccount,
            $this->account,
            $this->id
        ));
        $proposalId = $this->matchProposals->for($this->account)[0]->id();
        $this->handle(AcceptTheProposal::withId(
            $proposalId,
            $this->account,
            $this->id
        ));
        return $proposalId;
    }
}
