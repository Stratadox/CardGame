<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

interface RedirectSources
{
    public function named(string $name): RedirectSource;
}
