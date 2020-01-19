<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

final class TestClient
{
    private $clock;
    private $uri;
    private $resource;

    public function __construct(TestClock $clock)
    {
        $this->clock = $clock;
    }

    public function visit(string $page): void
    {

    }

    public function do(string $action, array $parameters = []): void
    {

    }

    /**
     * @param string $property
     * @return int|float|bool|string|array
     */
    public function see(string $property)
    {
        return 0;
    }

    /** @return string[] */
    public function flashMessages(): array
    {
        return [];
    }
}
