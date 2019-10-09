<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

use Stratadox\CardGame\CorrelationId;

final class Visit
{
    private $page;
    private $redirectSource;
    private $visitorId;
    private $correlationId;

    private function __construct(
        string $page,
        string $redirectSource,
        VisitorId $visitorId,
        CorrelationId $correlationId
    ) {
        $this->page = $page;
        $this->redirectSource = $redirectSource;
        $this->visitorId = $visitorId;
        $this->correlationId = $correlationId;
    }

    public static function page(
        string $page,
        string $redirectSource,
        VisitorId $visitorId,
        CorrelationId $correlationId
    ): self {
        return new self($page, $redirectSource, $visitorId, $correlationId);
    }

    public function whichPage(): string
    {
        return $this->page;
    }

    public function redirectSource(): string
    {
        return $this->redirectSource;
    }

    public function visitorId(): VisitorId
    {
        return $this->visitorId;
    }

    public function correlationId(): CorrelationId
    {
        return $this->correlationId;
    }
}
