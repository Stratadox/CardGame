<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

use DateTimeInterface;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Account\PlayerAccount;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;

final class Visitor implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $visitedPages = [];

    public function __construct(VisitorId $id)
    {
        $this->id = $id;
    }

    public function id(): VisitorId
    {
        return $this->id;
    }

    public function openAccount(AccountId $playerId): PlayerAccount
    {
        return PlayerAccount::fromVisitor($this->id, $playerId);
    }

    public function visit(
        string $page,
        DateTimeInterface $when,
        string $source
    ): void {
        $isFirstVisit = !isset($this->visitedPages[$page]);
        if ($isFirstVisit) {
            $this->visitedPages[$page] = $when;
        }
        $this->events[] = new VisitedPage(
            $this->id,
            $page,
            $source,
            $isFirstVisit
        );
    }
}
