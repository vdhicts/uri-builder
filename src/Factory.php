<?php

namespace Vdhicts\UriBuilder;

use Cyberfusion\DomainParser\Contracts\DomainParser as DomainParserContract;
use Cyberfusion\DomainParser\Data\ParsedDomain;
use Cyberfusion\DomainParser\Parser;
use InvalidArgumentException;
use Throwable;
use Vdhicts\HttpQueryBuilder\Parameter;

class Factory
{
    private DomainParserContract $domainParser;

    public function __construct(?DomainParserContract $domainParser = null)
    {
        $this->domainParser = $domainParser ?? new Parser();
    }

    private function prepareBasicAuthentication(?string $username, ?string $password): ?BasicAuthentication
    {
        if ($username === null || $password === null) {
            return null;
        }

        return new BasicAuthentication(
            username: $username,
            password: $password,
        );
    }

    private function parseDomain(?string $domain): ?ParsedDomain
    {
        if ($domain === null) {
            return null;
        }

        try {
            return $this
                ->domainParser
                ->domain($domain);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return array<string>
     */
    private function preparePaths(?string $path = null): array
    {
        if ($path === null) {
            return [];
        }

        return explode('/', ltrim($path, '/'));
    }

    /**
     * @return array<Parameter>
     */
    private function prepareQueryString(?string $queryString = null): array
    {
        $queryParameters = [];
        if ($queryString === null) {
            return $queryParameters;
        }

        $queryParts = explode('&', $queryString);
        foreach ($queryParts as $queryPart) {
            [$key, $value] = explode('=', $queryPart);

            $queryParameters[] = new Parameter(
                key: $key,
                value: $value,
            );
        }

        return $queryParameters;
    }

    public function build(string $url): Uri
    {
        $parsedUrl = parse_url($url);
        if ($parsedUrl === false) {
            throw new InvalidArgumentException('Unable to parse URL');
        }

        $parsedDomain = $this->parseDomain($parsedUrl['host'] ?? null);

        return new Uri(
            scheme: $parsedUrl['scheme'] ?? 'http',
            subdomain: $parsedDomain?->getSubdomain(),
            domain: $parsedDomain?->getSld(),
            topLevelDomain: $parsedDomain?->getTld(),
            port: $parsedUrl['port'] ?? null,
            paths: $this->preparePaths($parsedUrl['path'] ?? null),
            queryParameters: $this->prepareQueryString($parsedUrl['query'] ?? null),
            fragment: $parsedUrl['fragment'] ?? null,
            basicAuthentication: $this->prepareBasicAuthentication(
                username: $parsedUrl['user'] ?? null,
                password: $parsedUrl['pass'] ?? null,
            ),
        );
    }
}
