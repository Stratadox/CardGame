<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel;

use Stratadox\CardGame\CorrelationId;

final class Refusals
{
    private $errorMessages = [];

    /** @return string[] */
    public function for(CorrelationId $theRequest): array
    {
//        return ['Cannot open account for unknown entity'];
        return $this->errorMessages[$theRequest->id()] ?? [];
    }

    public function addFor(CorrelationId $theRequest, string $message): void
    {
        $this->errorMessages[$theRequest->id()][] = $message;
    }
}
