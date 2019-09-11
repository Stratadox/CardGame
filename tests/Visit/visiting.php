<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Visit;

use Stratadox\CardGame\Test\CardGameTest;
use Stratadox\CardGame\Visiting\Visit;
use Stratadox\CardGame\Visiting\VisitorId;

/**
 * @testdox visiting the information pages
 */
class visiting extends CardGameTest
{
    private $visitorId;
    private $otherVisitor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertEquals(0, $this->statistics->visitorsFrom('example.com'));
        $this->assertEquals(0, $this->statistics->visitsFrom('example.com'));
        $this->assertEquals(0, $this->statistics->visitorsOnPage('home'));
        $this->assertEquals(0, $this->statistics->visitsToPage('home'));

        $this->visitorId = VisitorId::from('fdoo');
        $this->otherVisitor = VisitorId::from('bfbfb');
    }

    /** @test */
    function counting_redirects_from_a_source()
    {
        $this->handle(
            Visit::page('home', 'example.com', $this->visitorId)
        );

        $this->assertEquals(1, $this->statistics->visitorsFrom('example.com'));
    }

    /** @test */
    function counting_unique_redirects_from_a_source()
    {
        $this->handle(
            Visit::page('home', 'example.com', $this->visitorId)
        );

        $this->handle(
            Visit::page('home', 'example.com', $this->visitorId)
        );

        $this->assertEquals(1, $this->statistics->visitorsFrom('example.com'));
    }

    /** @test */
    function counting_all_redirects_from_a_source()
    {
        $this->handle(
            Visit::page('home', 'example.com', $this->visitorId)
        );

        $this->handle(
            Visit::page('home', 'example.com', $this->visitorId)
        );

        $this->assertEquals(2, $this->statistics->visitsFrom('example.com'));
    }

    /** @test */
    function counting_page_visits()
    {
        $this->handle(
            Visit::page('home', 'example.com', $this->visitorId)
        );

        $this->assertEquals(1, $this->statistics->visitorsOnPage('home'));
        $this->assertEquals(1, $this->statistics->visitsToPage('home'));
    }

    /** @test */
    function counting_multiple_page_visits()
    {
        $this->handle(
            Visit::page('home', 'example.com', $this->visitorId)
        );
        $this->handle(
            Visit::page('home', 'example.com', $this->otherVisitor)
        );

        $this->assertEquals(2, $this->statistics->visitorsOnPage('home'));
        $this->assertEquals(2, $this->statistics->visitsToPage('home'));
    }

    /** @test */
    function counting_multiple_redirects_from_a_source()
    {
        $this->handle(
            Visit::page('home', 'example.com', $this->visitorId)
        );

        $this->handle(
            Visit::page('home', 'example.com', $this->otherVisitor)
        );

        $this->assertEquals(2, $this->statistics->visitorsFrom('example.com'));
    }

    /** @test */
    function counting_unique_page_visits()
    {
        $this->handle(
            Visit::page('home', 'example.com', $this->visitorId)
        );
        $this->handle(
            Visit::page('home', 'example.com', $this->visitorId)
        );

        $this->assertEquals(1, $this->statistics->visitorsOnPage('home'));
    }

    /** @test */
    function counting_all_page_visits()
    {
        $this->handle(
            Visit::page('home', 'example.com', $this->visitorId)
        );
        $this->handle(
            Visit::page('home', 'example.com', $this->visitorId)
        );

        $this->assertEquals(2, $this->statistics->visitsToPage('home'));
    }
}
