<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel;

final class PageVisitsStatisticsReport
{
    private $visitorsFrom = [];
    private $visitsFrom = [];
    private $visitorsOnPage = [];
    private $visitsToPage = [];

    public function visitorsFrom(string $source): int
    {
        return $this->visitorsFrom[$source] ?? 0;
    }

    public function visitsFrom(string $source): int
    {
        return $this->visitsFrom[$source] ?? 0;
    }

    public function visitorsOnPage(string $page): int
    {
        return $this->visitorsOnPage[$page] ?? 0;
    }

    public function visitsToPage(string $page): int
    {
        return $this->visitsToPage[$page] ?? 0;
    }

    public function visit(string $page, string $source): void
    {
        if (!isset($this->visitsToPage[$page])) {
            $this->visitsToPage[$page] = 0;
        }
        if (!isset($this->visitsFrom[$source])) {
            $this->visitsFrom[$source] = 0;
        }
        $this->visitsToPage[$page]++;
        $this->visitsFrom[$source]++;
    }

    public function firstVisitTo(string $page): void
    {
        $this->visitorsOnPage[$page] = isset($this->visitorsOnPage[$page]) ?
            $this->visitorsOnPage[$page] + 1 :
            1;
    }

    public function firstVisitFrom(string $source): void
    {
        $this->visitorsFrom[$source] = isset($this->visitorsFrom[$source]) ?
            $this->visitorsFrom[$source] + 1 :
            1;
    }
}
