<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\IdentityManagement;

use LogicException;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;
use Throwable;

abstract class IdGenerator
{
    private $uuidFactory;

    public function __construct(UuidFactoryInterface $uuidFactory)
    {
        $this->uuidFactory = $uuidFactory;
    }

    protected function newIdFor(string $type): UuidInterface
    {
        try {
            return $this->uuidFactory->uuid4();
        } catch (Throwable $uuidException) {
            throw new LogicException(sprintf(
                'Could not generate the %s id: %s',
                $type,
                $uuidException->getMessage()
            ), $uuidException->getCode(), $uuidException);
        }
    }
}
