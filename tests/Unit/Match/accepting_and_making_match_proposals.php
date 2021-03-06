<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Match;

use Stratadox\CardGame\Proposal\AcceptTheProposal;
use Stratadox\CardGame\Proposal\ProposalId;
use Stratadox\CardGame\Proposal\ProposeMatch;
use Stratadox\CardGame\Test\CardGameTest;
use Stratadox\CardGame\Visiting\VisitorId;

/**
 * @testdox accepting and making match proposals
 */
class accepting_and_making_match_proposals extends CardGameTest
{
    private $accountOne;
    private $accountTwo;
    private $proposalDuration = 30;
    private $aLittleTooLong;
    private $almostTooLong;
    private $allBegan;

    protected function setUp(): void
    {
        parent::setUp();

        $visitor1 = VisitorId::from('id-1');
        $visitor2 = VisitorId::from('id-2');
        $this->signUpForTheGame($visitor1);
        $this->signUpForTheGame($visitor2);

        $this->accountOne = $this->accountOverviews
            ->forVisitor($visitor1)
            ->id();
        $this->accountTwo = $this->accountOverviews
            ->forVisitor($visitor2)
            ->id();

        $this->aLittleTooLong = $this->interval(
            $this->proposalDuration + 1
        );

        $this->almostTooLong = $this->interval(
            $this->proposalDuration - 1
        );

        $this->allBegan = $this->clock->now();
    }

    /** @test */
    function no_proposals_until_a_match_is_proposed()
    {
        $this->assertEmpty($this->matchProposals->for($this->accountOne));
        $this->assertEmpty($this->matchProposals->for($this->accountTwo));
    }

    /** @test */
    function proposing_a_match_to_another_player()
    {
        $this->handle(ProposeMatch::between(
            $this->accountOne,
            $this->accountTwo,
            $this->id
        ));

        $this->assertEmpty($this->matchProposals->for($this->accountOne));
        $this->assertNotEmpty($this->matchProposals->for($this->accountTwo));
    }

    /** @test */
    function no_accepted_proposals_until_a_proposal_is_accepted()
    {
        $this->handle(ProposeMatch::between(
            $this->accountOne,
            $this->accountTwo,
            $this->id
        ));

        $this->assertEmpty($this->acceptedProposals->since($this->allBegan));
    }

    /** @test */
    function accepting_a_proposal()
    {
        $this->handle(ProposeMatch::between(
            $this->accountOne,
            $this->accountTwo,
            $this->id
        ));

        $this->handle(AcceptTheProposal::withId(
            $this->matchProposals->for($this->accountTwo)[0]->id(),
            $this->accountTwo,
            $this->id
        ));

        $this->assertNotEmpty(
            $this->acceptedProposals->since($this->allBegan)
        );
        $this->assertEmpty(
            $this->acceptedProposals->acceptedBy(
                $this->accountOne,
                $this->allBegan
            )
        );
        $this->assertNotEmpty(
            $this->acceptedProposals->acceptedBy(
                $this->accountTwo,
                $this->allBegan
            )
        );
        $this->assertNotEmpty(
            $this->acceptedProposals->proposedBy(
                $this->accountOne,
                $this->allBegan
            )
        );
        $this->assertEmpty(
            $this->acceptedProposals->proposedBy(
                $this->accountTwo,
                $this->allBegan
            )
        );
    }

    /** @test */
    function cannot_accept_non_existing_proposals()
    {
        $this->handle(AcceptTheProposal::withId(
            ProposalId::from('non-existing'),
            $this->accountTwo,
            $this->id
        ));

        $this->assertEmpty(
            $this->acceptedProposals->since($this->allBegan)
        );
        $this->assertEquals(
            ['Proposal not found'],
            $this->refusals->for($this->id)
        );
    }

    /** @test */
    function cannot_accept_expired_proposals()
    {
        $this->handle(ProposeMatch::between(
            $this->accountOne,
            $this->accountTwo,
            $this->id
        ));
        $proposalId = $this->matchProposals->for($this->accountTwo)[0]->id();

        $this->clock->fastForward($this->aLittleTooLong);
        $this->handle(AcceptTheProposal::withId(
            $proposalId,
            $this->accountTwo,
            $this->id
        ));

        $this->assertEmpty(
            $this->acceptedProposals->since($this->allBegan)
        );
        $this->assertEmpty(
            $this->acceptedProposals->proposedBy(
                $this->accountOne,
                $this->allBegan
            )
        );
        $this->assertEmpty(
            $this->acceptedProposals->acceptedBy(
                $this->accountTwo,
                $this->allBegan
            )
        );
        $this->assertEquals(
            ['The proposal has already expired!'],
            $this->refusals->for($this->id)
        );
    }

    /** @test */
    function accepting_a_proposal_just_in_time()
    {
        $this->handle(ProposeMatch::between(
            $this->accountOne,
            $this->accountTwo,
            $this->id
        ));
        $proposalId = $this->matchProposals->for($this->accountTwo)[0]->id();

        $this->clock->fastForward($this->almostTooLong);
        $this->handle(AcceptTheProposal::withId(
            $proposalId,
            $this->accountTwo,
            $this->id
        ));

        $this->assertNotEmpty(
            $this->acceptedProposals->since($this->allBegan)
        );
    }

    /** @test */
    function cannot_accept_proposals_for_others()
    {
        $this->handle(ProposeMatch::between(
            $this->accountOne,
            $this->accountTwo,
            $this->id
        ));
        $proposalId = $this->matchProposals->for($this->accountTwo)[0]->id();

        $this->handle(
            AcceptTheProposal::withId($proposalId, $this->accountOne, $this->id)
        );

        $this->assertEmpty($this->acceptedProposals->since($this->allBegan));
        $this->assertEquals(
            ['Proposal not found'],
            $this->refusals->for($this->id)
        );
    }
}
