<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test;

use Stratadox\CardGame\ReadModel\Account\NoAccountForVisitor;
use Stratadox\CardGame\Account\Command\OpenAnAccount;
use Stratadox\CardGame\Visiting\Command\Visit;
use Stratadox\CardGame\VisitorId;

/**
 * @testdox signing up for the game
 */
class signing_up_for_the_game extends CardGameTest
{
    private $visitorId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->visitorId = VisitorId::from('some-uuid-supposedly');
    }

    /** @test */
    function opening_a_guest_account()
    {
        $this->handle(Visit::page('home', 'source', $this->visitorId));
        $this->handle(OpenAnAccount::forVisitorWith($this->visitorId));

        $account = $this->accountOverviews->forVisitor($this->visitorId);

        $this->assertTrue($account->isGuestAccount());
        $this->assertNotEmpty($account->id());
    }

    /** @test */
    function not_opening_an_account_without_having_visited_any_page()
    {
        $this->handle(OpenAnAccount::forVisitorWith($this->visitorId));

        $this->expectException(NoAccountForVisitor::class);

        $this->accountOverviews->forVisitor($this->visitorId);
    }

    /** @test */
    function not_seeing_the_account_in_the_player_list_before_opening_it()
    {
        $this->handle(Visit::page('home', 'source', $this->visitorId));

        $this->assertEmpty($this->playerList);
    }

    /** @test */
    function seeing_the_guest_account_in_the_player_list()
    {
        $this->handle(Visit::page('home', 'source', $this->visitorId));
        $this->handle(OpenAnAccount::forVisitorWith($this->visitorId));

        $this->assertNotEmpty($this->playerList);
    }

    /** @test */
    function no_account_without_opening_one_first()
    {
        $this->expectException(NoAccountForVisitor::class);

        $this->accountOverviews->forVisitor($this->visitorId);
    }
}
