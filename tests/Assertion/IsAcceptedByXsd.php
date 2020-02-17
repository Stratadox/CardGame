<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test;

use DOMDocument;
use PHPUnit\Framework\Constraint\Constraint;
use function assert;
use function sprintf;

final class IsAcceptedByXsd extends Constraint
{
    /** @var string */
    private $xsdFile;

    private function __construct(string $xsdFile)
    {
        $this->xsdFile = $xsdFile;
    }

    public static function file(string $file): self
    {
        return new self($file);
    }

    protected function matches($other): bool
    {
        assert($other instanceof DOMDocument);
        return $other->schemaValidate($this->xsdFile);
    }

    public function toString(): string
    {
        return sprintf(
            'is accepted by the xsd in: %s',
            $this->xsdFile
        );
    }
}
