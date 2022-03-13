<?php

namespace Webdl\PantherCrawler\Config;

class ScraperConfig
{
    private function __construct(
        private string $startingUrl,
        private array $allowedDomains = [],
        private ?int $maxDepth = 3,
        private ?string $userAgent = null,
        private ?int $maxLinks = null,
    ) {
    }

    public static function create(string $startingUrl, array $allowedDomains = [], ?int $maxDepth = 3, ?string $userAgent = null, ?int $maxLinks = null): self
    {
        if (null === parse_url($startingUrl, PHP_URL_HOST) || null === parse_url($startingUrl, PHP_URL_SCHEME)) {
            throw new \UnexpectedValueException(sprintf('Starting URL "%s" must contain at least host and a scheme!', $startingUrl));
        }
        if (empty($allowedDomains)) {
            $allowedDomains = [parse_url($startingUrl, PHP_URL_HOST)];
        }
        return new self($startingUrl, $allowedDomains, $maxDepth, $userAgent, $maxLinks);
    }

    public function getStartingUrl(): string
    {
        return $this->startingUrl;
    }

    public function getMaxDepth(): int
    {
        return $this->maxDepth;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getMaxLinks(): ?int
    {
        return $this->maxLinks;
    }

    public function getAllowedDomains(): array
    {
        return $this->allowedDomains;
    }
}
