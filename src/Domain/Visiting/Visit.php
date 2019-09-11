<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

final class Visit
{
    private $page;
    private $redirectSource;
    private $visitorId;

    private function __construct(
        string $page,
        string $redirectSource,
        VisitorId $visitorId
    ) {
        $this->page = $page;
        $this->redirectSource = $redirectSource;
        $this->visitorId = $visitorId;
    }

    public static function page(
        string $page,
        string $redirectSource,
        VisitorId $visitorId
    ): self {
        return new self($page, $redirectSource, $visitorId);
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
}
