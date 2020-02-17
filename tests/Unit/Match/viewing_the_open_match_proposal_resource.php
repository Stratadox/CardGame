<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use DateInterval;
use Stratadox\CardGame\Proposal\ProposeMatch;
use Stratadox\CardGame\Test\CardGameTest;
use Stratadox\CardGame\Visiting\VisitorId;

/**
 * @testdox viewing the open match match proposal resource
 */
class viewing_the_open_match_proposal_resource extends CardGameTest
{
    private $account;
    private $otherAccount;
    private $thirdAccount;
    private $allBegan;
    private $expire;

    protected function setUp(): void
    {
        $this->markTestSkipped('@todo');

        parent::setUp();

        $visitor1 = VisitorId::from('id-1');
        $visitor2 = VisitorId::from('id-2');
        $visitor3 = VisitorId::from('id-3');
        $this->signUpForTheGame($visitor1);
        $this->signUpForTheGame($visitor2);
        $this->signUpForTheGame($visitor3);

        $this->account = $this->accountOverviews
            ->forVisitor($visitor1)
            ->id();
        $this->otherAccount = $this->accountOverviews
            ->forVisitor($visitor2)
            ->id();
        $this->thirdAccount = $this->accountOverviews
            ->forVisitor($visitor3)
            ->id();

        $this->allBegan = $this->clock->now();
        $this->expire = $this->allBegan->add(
            DateInterval::createFromDateString('30 seconds')
        );

        $this->authenticateAs($this->account);
    }

    /** @test */
    function viewing_an_empty_list_of_the_proposals_to_accept_as_json()
    {
        $this->assertJsonStringEqualsJsonString(
            $this->fileContentsWithTagsReplaced(
                ['(ACCOUNT)' => $this->account],
                __DIR__ . '/../../../schema/proposals/v1/example/open/empty.json'
            ),
            $this->toJson($this->matchProposals->for($this->account))
        );
    }

    /** @test */
    function viewing_an_empty_list_of_the_proposals_to_accept_as_xml()
    {
        $this->assertXmlStringEqualsXmlString(
            $this->fileContentsWithTagsReplaced(
                ['(ACCOUNT)' => $this->account],
                __DIR__ . '/../../../schema/proposals/v1/example/open/empty.xml'
            ),
            $this->toXml($this->matchProposals->for($this->account))
        );
    }

    /** @test */
    function viewing_a_list_with_an_open_proposal_as_json()
    {
        $this->handle(ProposeMatch::between(
            $this->otherAccount,
            $this->account,
            $this->id
        ));

        $this->assertJsonStringEqualsJsonString(
            $this->fileContentsWithTagsReplaced(
                [
                    '(ACCOUNT)' => $this->account,
                    '(OTHER-ACCOUNT)' => $this->otherAccount,
                    '(PROPOSAL)' => $this->matchProposals->for($this->account)[0]->id(),
                    '(EXPIRE)' => $this->expire->format('c'),
                ],
                __DIR__ . '/../../../schema/proposals/v1/example/open/one.json'
            ),
            $this->toJson($this->matchProposals->for($this->account))
        );
    }

    /** @test */
    function viewing_a_list_with_an_open_proposal_as_xml()
    {
        $this->handle(ProposeMatch::between(
            $this->otherAccount,
            $this->account,
            $this->id
        ));

        $this->assertXmlStringEqualsXmlString(
            $this->fileContentsWithTagsReplaced(
                [
                    '(ACCOUNT)' => $this->account,
                    '(OTHER-ACCOUNT)' => $this->otherAccount,
                    '(PROPOSAL)' => $this->matchProposals->for($this->account)[0]->id(),
                    '(EXPIRE)' => $this->expire->format('c'),
                ],
                __DIR__ . '/../../../schema/proposals/v1/example/open/one.xml'
            ),
            $this->toXml($this->matchProposals->for($this->account))
        );
    }

    /** @test */
    function viewing_a_list_with_two_open_proposals_as_json()
    {
        $this->handle(ProposeMatch::between(
            $this->otherAccount,
            $this->account,
            $this->id
        ));
        $this->handle(ProposeMatch::between(
            $this->thirdAccount,
            $this->account,
            $this->id
        ));

        $proposals = $this->matchProposals->for($this->account);

        $this->assertJsonStringEqualsJsonString(
            $this->fileContentsWithTagsReplaced(
                [
                    '(ACCOUNT)' => $this->account,
                    '(OTHER-ACCOUNT)' => $this->otherAccount,
                    '(PROPOSAL)' => $proposals[0]->id(),
                    '(PROPOSAL2)' => $proposals[1]->id(),
                    '(EXPIRE)' => $this->expire->format('c'),
                ],
                __DIR__ . '/../../../schema/proposals/v1/example/open/two.json'
            ),
            $this->toJson($this->matchProposals->for($this->account))
        );
    }

    /** @test */
    function viewing_a_list_with_two_open_proposals_as_xml()
    {
        $this->handle(ProposeMatch::between(
            $this->otherAccount,
            $this->account,
            $this->id
        ));
        $this->handle(ProposeMatch::between(
            $this->thirdAccount,
            $this->account,
            $this->id
        ));

        $proposals = $this->matchProposals->for($this->account);

        $this->assertXmlStringEqualsXmlString(
            $this->fileContentsWithTagsReplaced(
                [
                    '(ACCOUNT)' => $this->account,
                    '(OTHER-ACCOUNT)' => $this->otherAccount,
                    '(PROPOSAL)' => $proposals[0]->id(),
                    '(PROPOSAL2)' => $proposals[1]->id(),
                    '(EXPIRE)' => $this->expire->format('c'),
                ],
                __DIR__ . '/../../../schema/proposals/v1/example/open/two.xml'
            ),
            $this->toXml($this->matchProposals->for($this->account))
        );
    }
}
