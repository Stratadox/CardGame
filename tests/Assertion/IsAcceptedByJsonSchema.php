<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test;

use JsonSchema\Validator;
use PHPUnit\Framework\Constraint\Constraint;
use function assert;
use function is_string;
use function json_decode;
use function sprintf;
use function var_export;

final class IsAcceptedByJsonSchema extends Constraint
{
    /** @var string */
    private $jsonSchemaFile;
    /** @var Validator */
    private $validator;

    private function __construct(string $jsonSchemaFile, Validator $validator)
    {
        $this->jsonSchemaFile = $jsonSchemaFile;
        $this->validator = $validator;
    }

    public static function file(string $file): self
    {
        return new self($file, new Validator());
    }

    protected function matches($other): bool
    {
        assert(is_string($other));
        $json = json_decode($other, false);
        $this->validator->validate($json, (object)['$ref' => 'file://'.$this->jsonSchemaFile]);
        return $this->validator->isValid();
    }

    public function toString(): string
    {
        $message = '';
        foreach ($this->validator->getErrors() as $error) {
            $message .= sprintf("%s\n", var_export($error, true));
        }
        return sprintf(
            "is accepted by the json schema in: %s\n\n%s",
            $this->jsonSchemaFile,
            $message
        );
    }
}
