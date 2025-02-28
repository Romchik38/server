<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Urlbuilder;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;

class Url
{
    protected readonly string $prefix;

    /** @var array<int,string> $schemes */
    protected array $schemes = ['http', 'https'];

    public function __construct(
        string $scheme = '',
        string $authority = ''
    ) {
        if ($scheme === '' && $authority === '') {
            $this->prefix = '';
        } else {
            if ($scheme === '') {
                throw new InvalidArgumentException('Scheme is empty');
            } elseif (!in_array($scheme, $this->schemes)) {
                throw new InvalidArgumentException('Invalid scheme: ' . $scheme);
            }
            if ($authority === '') {
                throw new InvalidArgumentException('Authority is empty');
            }

            $this->prefix = sprintf('%s://%s', $scheme, $authority);
        }
    }

    public static function fromRequest(RequestInterface $request): self
    {
        $uri = $request->getUri();

        return new self($uri->getScheme(), $uri->getAuthority());
    }
}