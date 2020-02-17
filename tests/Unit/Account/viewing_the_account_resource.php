<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test\Account;

use Stratadox\CardGame\Test\CardGameTest;
use Stratadox\CardGame\Visiting\VisitorId;

/**
 * @testdox viewing the account resource
 */
class viewing_the_account_resource extends CardGameTest
{
    private $visitorId;
    private $otherVisitorId;
    private $myAccount;
    private $otherAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->visitorId = VisitorId::from('some-uuid-supposedly');
        $this->otherVisitorId = VisitorId::from('another-uuid-I-guess');

        $this->signUpForTheGame($this->visitorId);
        $this->signUpForTheGame($this->otherVisitorId);

        $this->myAccount = $this->accountOverviews->forVisitor($this->visitorId);
        $this->otherAccount = $this->accountOverviews->forVisitor($this->otherVisitorId);

        $this->authenticateAs($this->myAccount->id());
    }

    /** @test */
    function viewing_the_account_resource_as_json()
    {
        $this->assertJsonStringEqualsJsonString(
            $this->fileContentsWithTagsReplaced(
                [
                    '(ID)' => $this->myAccount->id(),
                    '(VISITOR)' => $this->visitorId,
                ],
                __DIR__ . '/../../../schema/account/v1/example/overview-mine.json'
            ),
            $this->toJson($this->myAccount)
        );
        $this->assertJsonStringIsAcceptedByJsonSchemaFile(
            __DIR__ . '/../../../schema/account/v1/overview.schema.json',
            $this->toJson($this->myAccount)
        );
    }

    /** @test */
    function viewing_my_account_resource_as_xml()
    {
        $this->assertXmlStringEqualsXmlString(
            $this->fileContentsWithTagsReplaced(
                [
                    '(ID)' => $this->myAccount->id(),
                    '(VISITOR)' => $this->visitorId,
                ],
                __DIR__ . '/../../../schema/account/v1/example/overview-mine.xml'
            ),
            $this->toXml($this->myAccount)
        );
        $this->assertXmlStringIsAcceptedByXsdFile(
            __DIR__ . '/../../../schema/account/v1/overview.xsd',
            $this->toXml($this->myAccount)
        );
    }

    /** @test */
    function viewing_another_account_resource_as_json()
    {
        $this->assertJsonStringEqualsJsonString(
            $this->fileContentsWithTagsReplaced(
                [
                    '(ID)' => $this->otherAccount->id(),
                    '(ME)' => $this->myAccount->id(),
                    '(VISITOR)' => $this->otherVisitorId,
                ],
                __DIR__ . '/../../../schema/account/v1/example/overview-other.json'
            ),
            $this->toJson($this->otherAccount)
        );
        $this->assertJsonStringIsAcceptedByJsonSchemaFile(
            __DIR__ . '/../../../schema/account/v1/overview.schema.json',
            $this->toJson($this->myAccount)
        );
    }

    /** @test */
    function viewing_another_account_resource_as_xml()
    {
        $this->assertXmlStringEqualsXmlString(
            $this->fileContentsWithTagsReplaced(
                [
                    '(ID)' => $this->otherAccount->id(),
                    '(ME)' => $this->myAccount->id(),
                    '(VISITOR)' => $this->otherVisitorId,
                ],
                __DIR__ . '/../../../schema/account/v1/example/overview-other.xml'
            ),
            $this->toXml($this->otherAccount)
        );
        $this->assertXmlStringIsAcceptedByXsdFile(
            __DIR__ . '/../../../schema/account/v1/overview.xsd',
            $this->toXml($this->otherAccount)
        );
    }
}
