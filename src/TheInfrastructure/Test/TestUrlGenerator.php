<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Hateoas\UrlGenerator\UrlGeneratorInterface;
use function array_keys;
use function array_map;
use function array_values;
use function str_replace;

final class TestUrlGenerator implements UrlGeneratorInterface
{
    private const ROUTES = [
        'account:overview' => 'account/{account}/',
        'proposals:open' => 'match/proposals/open/{account}/',
        'proposals:accepted' => 'match/proposals/accepted/{account}/{?since}',
        'proposals:successful' => 'match/proposals/successful/{account}/{?since}',
        'proposals:propose' => 'match/propose/{from}/vs/{to}/',
    ];
    private $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function generate(
        string $name,
        array $parameters,
        $absolute = false
    ): string {
        return $this->prefix . str_replace(
            array_map(static function (string $parameter): string {
                return '{' . $parameter . '}';
            }, array_keys($parameters)),
            array_values($parameters),
            self::ROUTES[$name]
        );
    }
}
