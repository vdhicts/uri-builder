<?php

namespace Vdhicts\UriBuilder;

use InvalidArgumentException;
use Stringable;
use Illuminate\Support\Stringable as Str;
use Vdhicts\HttpQueryBuilder\Parameter;

class Uri implements Stringable
{
    /**
     * @var array<string|int>
     */
    private array $paths = [];

    /**
     * @var array<Parameter>
     */
    private array $queryParameters = [];

    /**
     * @param array<string|int> $paths
     * @param array<Parameter> $queryParameters
     */
    public function __construct(
        private string $scheme = 'http',
        private ?string $subdomain = null,
        private ?string $domain = null,
        private ?string $topLevelDomain = null,
        private ?int $port = null,
        array $paths = [],
        array $queryParameters = [],
        private ?string $fragment = null,
        private ?BasicAuthentication $basicAuthentication = null,
    ) {
        $this->setPaths($paths);
        $this->setQueryParameters($queryParameters);
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;
        return $this;
    }

    public function getSubdomain(): ?string
    {
        return $this->subdomain;
    }

    public function hasSubdomain(): bool
    {
        return $this->subdomain !== null;
    }

    public function setSubdomain(?string $subdomain): self
    {
        $this->subdomain = $subdomain;
        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function hasDomain(): bool
    {
        return $this->domain !== null;
    }

    public function setDomain(?string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    public function getTopLevelDomain(): ?string
    {
        return $this->topLevelDomain;
    }

    public function hasTopLevelDomain(): bool
    {
        return $this->topLevelDomain !== null;
    }

    public function setTopLevelDomain(?string $topLevelDomain): self
    {
        $this->topLevelDomain = $topLevelDomain;
        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function hasPort(): bool
    {
        return $this->port !== null;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setPort(?int $port): self
    {
        if ($port !== null && ($port < 1 || $port > 65535)) {
            throw new InvalidArgumentException('Port must be between 1 and 65535');
        }

        $this->port = $port;
        return $this;
    }

    /**
     * @return array<string|int>
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    public function addPath(string $path): self
    {
        $this->paths[] = $path;

        return $this;
    }

    public function hasPaths(): bool
    {
        return count($this->paths) !== 0;
    }

    /**
     * @param array<string|int> $paths
     * @throws InvalidArgumentException
     */
    public function setPaths(array $paths): self
    {
        $this->paths = [];

        foreach ($paths as $path) {
            if (! is_string($path) && ! is_numeric($path)) {
                throw new InvalidArgumentException('Paths must contain strings or integers');
            }

            $this->addPath($path);
        }

        return $this;
    }

    /**
     * @return array<Parameter>
     */
    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    public function addQueryParameter(Parameter $parameter): self
    {
        $this->queryParameters[] = $parameter;
        return $this;
    }

    public function hasQueryParameters(): bool
    {
        return count($this->queryParameters) !== 0;
    }

    /**
     * @param array<Parameter> $parameters
     * @throws InvalidArgumentException
     */
    public function setQueryParameters(array $parameters): self
    {
        $this->queryParameters = [];

        foreach ($parameters as $parameter) {
            if (! $parameter instanceof Parameter) {
                throw new InvalidArgumentException('Query parameters must be instances of Parameter');
            }

            $this->addQueryParameter($parameter);
        }

        return $this;
    }

    public function getFragment(): ?string
    {
        return $this->fragment;
    }

    public function hasFragment(): bool
    {
        return $this->fragment !== null;
    }

    public function setFragment(?string $fragment): self
    {
        $this->fragment = $fragment;
        return $this;
    }

    public function getBasicAuthentication(): ?BasicAuthentication
    {
        return $this->basicAuthentication;
    }

    public function hasBasicAuthentication(): bool
    {
        return $this->basicAuthentication !== null;
    }

    public function setBasicAuthentication(?BasicAuthentication $basicAuthentication): Uri
    {
        $this->basicAuthentication = $basicAuthentication;
        return $this;
    }

    public function isAbsolute(): bool
    {
        return $this->hasDomain() && $this->hasTopLevelDomain();
    }

    public function isRelative(): bool
    {
        return ! $this->isAbsolute();
    }

    public function toString(): string
    {
        $relativeUrl = (new Str())
            ->when(
                $this->hasPaths(),
                fn (Str $stringable) => $stringable
                    ->append('/')
                    ->append(implode('/', $this->getPaths()))
            )
            ->when(
                $this->hasQueryParameters(),
                fn (Str $stringable) => $stringable
                    ->append('?')
                    ->append(implode('&', array_map(
                        static fn (Parameter $parameter): string => $parameter->toString(),
                        $this->getQueryParameters()
                    )))
            )
            ->when(
                $this->hasFragment(),
                fn (Str $stringable) => $stringable
                    ->append('#')
                    ->append($this->getFragment())
            );
        if ($this->isRelative()) {
            return $relativeUrl->toString();
        }

        return (new Str())
            ->append($this->getScheme())
            ->append('://')
            ->when(
                $this->hasBasicAuthentication(),
                fn (Str $stringable) => $stringable
                    ->append($this->getBasicAuthentication()?->toString())
                    ->append('@')
            )
            ->when(
                $this->hasSubdomain(),
                fn (Str $stringable) => $stringable
                    ->append($this->getSubdomain())
                    ->append('.')
            )
            ->append($this->getDomain())
            ->append('.')
            ->append($this->getTopLevelDomain())
            ->when(
                $this->hasPort(),
                fn (Stringable $stringable) => $stringable
                    ->append(':')
                    ->append((string) $this->getPort())
            )
            ->append($relativeUrl)
            ->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
