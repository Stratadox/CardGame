<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CardGame\Visiting\RedirectSource;
use Stratadox\CardGame\Visiting\RedirectSources;

final class InMemoryRedirectSources implements RedirectSources
{
    private $sources = [];

    public function named(string $name): RedirectSource
    {
        if (!isset($this->sources[$name])) {
            $this->sources[$name] = new RedirectSource($name);
        }
        return $this->sources[$name];
    }
}
