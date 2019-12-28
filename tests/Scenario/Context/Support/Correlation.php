<?php declare(strict_types=1);

namespace Stratadox\CardGame\Context\Support;

use Stratadox\CardGame\CorrelationId;

trait Correlation
{
    public function correlation($id = 'some-correlation-id'): CorrelationId
    {
        return CorrelationId::from($id);
    }
}
