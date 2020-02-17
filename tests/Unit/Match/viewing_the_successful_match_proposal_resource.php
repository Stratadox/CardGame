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
 * @testdox viewing the successful match match proposal resource
 */
class viewing_the_successful_match_proposal_resource extends CardGameTest
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
    }

    /** @test */
    function viewing_an_empty_list_of_the_successful_proposals_as_json()
    {
        $this->assertJsonStringEqualsJsonString(
            $this->replace(
                ['{ACCOUNT}' => $this->account],
                '{"successful-proposals": {
                    "proposals": [],
                    "links": [
                        {
                            "href": "test://match/proposals/successful/{ACCOUNT}",
                            "rel": "self",
                            "type": "GET"
                        },
                        {
                            "href": "test://account/{ACCOUNT}",
                            "rel": "account",
                            "type": "GET"
                        }
                    ]
                }}'
            ),
            $this->toJson($this->matchProposals->acceptedFrom($this->account, $this->allBegan))
        );
    }

    /** @test */
    function viewing_an_empty_list_of_the_successful_proposals_as_xml()
    {
        $this->assertXmlStringEqualsXmlString(
            $this->replace(
                ['{ACCOUNT}' => $this->account],
                '<?xml version="1.0"?>
                <successful-proposals>
                    <proposals />
                    <links>
                        <link href="test://match/proposals/successful/{ACCOUNT}" rel="self" type="GET" />
                        <link href="test://account/{ACCOUNT}" rel="account" type="GET" />
                    </links>
                </successful-proposals>'
            ),
            $this->toXml($this->matchProposals->acceptedFrom($this->account, $this->allBegan))
        );
    }

    /** @test */
    function viewing_a_list_with_an_successful_proposal_as_json()
    {
        $proposalId = $this->proposeAndAccept();

        $this->assertJsonStringEqualsJsonString(
            $this->replace(
                [
                    '{ACCOUNT}' => $this->account,
                    '{OTHER-ACCOUNT}' => $this->otherAccount,
                    '{PROPOSAL}' => $proposalId,
                    '{EXPIRE}' => $this->expire->format('c'),
                ],
                '{"successful-proposals": {
                    "proposals": [
                        {"proposal-overview": {
                            "id": "{PROPOSAL}",
                            "from": "{ACCOUNT}",
                            "to": "{OTHER-ACCOUNT}",
                            "valid-until": "{EXPIRE}",
                            "links": [
                                {
                                    "href": "test://match/proposals/{PROPOSAL}",
                                    "rel": "self",
                                    "type": "GET"
                                },
                                {
                                    "href": "test://account/{ACCOUNT}",
                                    "rel": "from",
                                    "type": "GET"
                                },
                                {
                                    "href": "test://account/{OTHER-ACCOUNT}",
                                    "rel": "to",
                                    "type": "GET"
                                }
                            ]
                        }}
                    ],
                    "links": [
                        {
                            "href": "test://match/proposals/successful/{ACCOUNT}",
                            "rel": "self",
                            "type": "GET"
                        },
                        {
                            "href": "test://account/{ACCOUNT}",
                            "rel": "account",
                            "type": "GET"
                        }
                    ]
                }}'
            ),
            $this->toJson($this->matchProposals->acceptedFrom($this->account, $this->allBegan))
        );
    }

    /** @test */
    function viewing_a_list_with_an_successful_proposal_as_xml()
    {
        $proposalId = $this->proposeAndAccept();

        $this->assertXmlStringEqualsXmlString(
            $this->replace(
                [
                    '{ACCOUNT}' => $this->account,
                    '{OTHER-ACCOUNT}' => $this->otherAccount,
                    '{PROPOSAL}' => $proposalId,
                    '{EXPIRE}' => $this->expire->format('c'),
                ],
                '<?xml version="1.0"?>
                <successful-proposals>
                    <proposals>
                        <proposal>
                            <proposal-overview id="{PROPOSAL}" from="{ACCOUNT}" to="{OTHER-ACCOUNT}" valid-until="{EXPIRE}">
                                <links>
                                    <link href="test://match/proposals/{PROPOSAL}" rel="self" type="GET" />
                                    <link href="test://account/{ACCOUNT}" rel="from" type="GET" />
                                    <link href="test://account/{OTHER-ACCOUNT}" rel="to" type="GET" />
                                </links>
                            </proposal-overview>
                        </proposal>
                    </proposals>
                    <links>
                        <link href="test://match/proposals/successful/{ACCOUNT}" rel="self" type="GET" />
                        <link href="test://account/{ACCOUNT}" rel="account" type="GET" />
                    </links>
                </successful-proposals>'
            ),
            $this->toXml($this->matchProposals->acceptedFrom($this->account, $this->allBegan))
        );
    }

    /** @test */
    function viewing_a_list_with_a_started_proposal_as_json()
    {
        $proposalId = $this->proposeAndAccept();
        $this->handle(StartTheMatch::forProposal($proposalId, $this->id));
        $matchId = $this->ongoingMatches->forProposal($proposalId)->id();

        $this->assertJsonStringEqualsJsonString(
            $this->replace(
                [
                    '{ACCOUNT}' => $this->account,
                    '{OTHER-ACCOUNT}' => $this->otherAccount,
                    '{PROPOSAL}' => $proposalId,
                    '{MATCH}' => $matchId,
                    '{EXPIRE}' => $this->expire->format('c'),
                ],
                '{"successful-proposals": {
                    "proposals": [
                        {"proposal-overview": {
                            "id": "{PROPOSAL}",
                            "from": "{ACCOUNT}",
                            "to": "{OTHER-ACCOUNT}",
                            "valid-until": "{EXPIRE}",
                            "links": [
                                {
                                    "href": "test://match/proposals/{PROPOSAL}",
                                    "rel": "self",
                                    "type": "GET"
                                },
                                {
                                    "href": "test://account/{ACCOUNT}",
                                    "rel": "from",
                                    "type": "GET"
                                },
                                {
                                    "href": "test://account/{OTHER-ACCOUNT}",
                                    "rel": "to",
                                    "type": "GET"
                                },
                                {
                                    "href": "test://match/{MATCH}",
                                    "rel": "play",
                                    "type": "GET"
                                }
                            ]
                        }}
                    ],
                    "links": [
                        {
                            "href": "test://match/proposals/successful/{ACCOUNT}",
                            "rel": "self",
                            "type": "GET"
                        },
                        {
                            "href": "test://account/{ACCOUNT}",
                            "rel": "account",
                            "type": "GET"
                        }
                    ]
                }}'
            ),
            $this->toJson($this->matchProposals->acceptedFrom($this->account, $this->allBegan))
        );
    }

    /** @test */
    function viewing_a_list_with_a_started_proposal_as_xml()
    {
        $proposalId = $this->proposeAndAccept();
        $this->handle(StartTheMatch::forProposal($proposalId, $this->id));
        $matchId = $this->ongoingMatches->forProposal($proposalId)->id();

        $this->assertXmlStringEqualsXmlString(
            $this->replace(
                [
                    '{ACCOUNT}' => $this->account,
                    '{OTHER-ACCOUNT}' => $this->otherAccount,
                    '{PROPOSAL}' => $proposalId,
                    '{MATCH}' => $matchId,
                    '{EXPIRE}' => $this->expire->format('c'),
                ],
                '<?xml version="1.0"?>
                <successful-proposals>
                    <proposals>
                        <proposal>
                            <proposal-overview id="{PROPOSAL}" from="{ACCOUNT}" to="{OTHER-ACCOUNT}" valid-until="{EXPIRE}">
                                <links>
                                    <link href="test://match/proposals/{PROPOSAL}" rel="self" type="GET" />
                                    <link href="test://account/{ACCOUNT}" rel="from" type="GET" />
                                    <link href="test://account/{OTHER-ACCOUNT}" rel="to" type="GET" />
                                    <link href="test://match/{MATCH}" rel="play" type="GET" />
                                </links>
                            </proposal-overview>
                        </proposal>
                    </proposals>
                    <links>
                        <link href="test://match/proposals/successful/{ACCOUNT}" rel="self" type="GET" />
                        <link href="test://account/{ACCOUNT}" rel="account" type="GET" />
                    </links>
                </successful-proposals>'
            ),
            $this->toXml($this->matchProposals->acceptedFrom($this->account, $this->allBegan))
        );
    }

    private function proposeAndAccept(): ProposalId
    {
        $this->handle(ProposeMatch::between(
            $this->account,
            $this->otherAccount,
            $this->id
        ));
        $proposalId = $this->matchProposals->for($this->otherAccount)[0]->id();
        $this->handle(AcceptTheProposal::withId(
            $proposalId,
            $this->otherAccount,
            $this->id
        ));
        return $proposalId;
    }
}
