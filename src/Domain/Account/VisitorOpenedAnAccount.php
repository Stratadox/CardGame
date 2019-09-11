<?php declare(strict_types=1);

namespace Stratadox\CardGame\Account;

use Stratadox\CardGame\Visiting\VisitorId;

final class VisitorOpenedAnAccount implements AccountEvent
{
    private $playerId;
    private $visitorId;

    private function __construct(AccountId $forPlayer, VisitorId $fromVisitor)
    {
        $this->playerId = $forPlayer;
        $this->visitorId = $fromVisitor;
    }

    public static function with(
        AccountId $id,
        VisitorId $visitorId
    ): AccountEvent {
        return new self($id, $visitorId);
    }

    public function aggregateId(): AccountId
    {
        return $this->playerId;
    }

    public function forVisitor(): VisitorId
    {
        return $this->visitorId;
    }

    public function payload(): array
    {
        return [
            'visitor' => $this->forVisitor(),
        ];
    }
}
