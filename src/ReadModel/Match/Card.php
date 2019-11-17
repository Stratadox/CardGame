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

    public function __construct(int $offset, CardTemplate $template)
    {
        $this->offset = $offset;
        $this->template = $template;
    }

    public function isAttacking(): bool
    {
        return $this->isAttacking;
    }

    public function template(): CardTemplate
    {
        return $this->template;
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function attack(): void
    {
        $this->isAttacking = true;
    }

    public function regroup(): void
    {
        $this->isAttacking = false;
    }
}
