<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\Urlbuilder;

use InvalidArgumentException;
use Romchik38\Server\Http\Controller\Path;
use Romchik38\Server\Http\Controller\PathInterface;

use function count;
use function implode;
use function in_array;
use function sprintf;
use function strlen;

class Urlbuilder implements UrlbuilderInterface
{
    protected readonly string $prefix;

    /** @var array<int,string> $schemes */
    protected array $schemes = ['http', 'https'];

    /**
     * Use two cases to build the url:
     *   case 1: relative url - scheme and authority must be empty both
     *   case 2: absolute url - scheme and authority non empty both and scheme http or https
     */
    public function __construct(
        protected readonly TargetInterface $target,
        string $scheme = '',
        string $authority = ''
    ) {
        if ($scheme === '' && $authority === '') {
            $this->prefix = '';
        } else {
            if ($scheme === '') {
                throw new InvalidArgumentException('Scheme is empty');
            } elseif (! in_array($scheme, $this->schemes)) {
                throw new InvalidArgumentException('Invalid scheme: ' . $scheme);
            }
            if ($authority === '') {
                throw new InvalidArgumentException('Authority is empty');
            }

            $this->prefix = sprintf('%s://%s', $scheme, $authority);
        }
    }

    public function fromArray(
        array $parts,
        array $params = [],
        string $fragment = ''
    ): string {
        $path = new Path($parts);
        return $this->fromPath($path, $params, $fragment);
    }

    public function fromPath(
        PathInterface $path,
        array $params = [],
        string $fragment = ''
    ): string {
        $fragmentPart = $fragment;
        if (strlen($fragment) !== 0) {
            $fragmentPart = '#' . $fragment;
        }
        $paramPart  = '';
        $paramItems = [];
        foreach ($params as $key => $value) {
            $paramItems[] = sprintf('%s=%s', $key, $value);
        }
        if (count($paramItems) > 0) {
            $paramPart = '?' . implode('&', $paramItems);
        }
        $targetPart = $this->target->fromPath($path);
        return $this->prefix . $targetPart . $paramPart . $fragmentPart;
    }
}
