<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

final class Card
{
    /** @var int */
    private $offset;
    /** @var CardTemplate */
    private $template;
    /** @var bool */
    private $isAttacking = false;
    /** @var bool */
    private $isDefending = false;

    public function __construct(int $offset, CardTemplate $template)
    {
        $this->offset = $offset;
        $this->template = $template;
    }

    public function isAttacking(): bool
    {
        return $this->isAttacking;
    }

    public function isDefending(): bool
    {
        return $this->isDefending;
    }

    public function hasTemplate(CardTemplate $template): bool
    {
        return $this->template->is($template);
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function attack(): void
    {
        $this->isAttacking = true;
    }

    public function defend(): void
    {
        $this->isDefending = true;
    }

    public function regroup(): void
    {
        $this->isAttacking = false;
    }
}
