<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel;

use Stratadox\CardGame\CorrelationId;

final class Refusals
{
    private $errorMessages = [];

    /** @return string[] */
    public function for(CorrelationId $request): array
    {
//        return ['Cannot open account for unknown entity'];
        return $this->errorMessages[$request->id()] ?? [];
    }

    public function addFor(CorrelationId $request, string $message): void
    {
        $this->errorMessages[$request->id()][] = $message;
    }
}
