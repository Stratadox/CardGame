<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\System;

use Exception;
use LogicException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidFactoryInterface;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultAccountIdGenerator;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultMatchIdGenerator;
use Stratadox\CardGame\Infrastructure\IdentityManagement\DefaultProposalIdGenerator;

/**
 * @testdox server configuration test
 * @todo do we really need these?
 */
class ServerConfigurationTest extends TestCase
{
    /** @test */
    function what_if_we_cannot_generate_account_uuids()
    {
        $generator = new DefaultAccountIdGenerator($this->throwingGenerator());

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Could not generate the account id: That is expected in this test.'
        );

        $generator->generate();
    }

    /** @test */
    function what_if_we_cannot_generate_match_uuids()
    {
        $generator = new DefaultMatchIdGenerator($this->throwingGenerator());

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Could not generate the match id: That is expected in this test.'
        );

        $generator->generate();
    }

    /** @test */
    function what_if_we_cannot_generate_proposal_uuids()
    {
        $generator = new DefaultProposalIdGenerator($this->throwingGenerator());

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Could not generate the proposal id: That is expected in this test.'
        );

        $generator->generate();
    }

    private function throwingGenerator(): UuidFactoryInterface
    {
        $factory = $this->createMock(UuidFactoryInterface::class);
        $factory->method('uuid4')->willThrowException(
            new Exception('That is expected in this test.')
        );
        return $factory;
    }
}
