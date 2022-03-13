<?php

namespace Webdl\PantherCrawler\Scraper;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Panther\Client;
use Webdl\PantherCrawler\Config\ScraperConfig;
use Webdl\PantherCrawler\Event\BeforeCrawlEvent;
use Webdl\PantherCrawler\Event\PageCrawledEvent;
use Webdl\PantherCrawler\Exception\MaxUrlsCrawledException;

class Scraper
{
    private string $baseUri;
    private array $urlsToCrawl = [];
    private array $crawledUrls = [];
    private int $crawledUrlsCount = 0;

    public function __construct(private Client $pantherClient, private ScraperConfig $scraperConfig, private ?EventDispatcher $eventDispatcher = null)
    {
        $this->baseUri = $this->scraperConfig->getStartingUrl();
    }

    public function crawl(): void
    {
        $this->urlsToCrawl = [$this->scraperConfig->getStartingUrl()];
        do {
            $urlToCrawl = array_shift($this->urlsToCrawl);
            try {
                $this->crawlPage($urlToCrawl);
                $this->crawledUrls[] = $urlToCrawl;
            } catch (MaxUrlsCrawledException) {
                // Force process finish
                $this->urlsToCrawl = [];
            }
        } while (!empty($this->urlsToCrawl));
    }

    private function isAllowedDomain($linkUrl): bool
    {
        $parsedDomain = parse_url($linkUrl, PHP_URL_HOST);
        if (empty($parsedDomain)) {
            return true;
        }
        return \in_array($parsedDomain, $this->scraperConfig->getAllowedDomains(), true);
    }

    private function buildUrl($linkUrl): string
    {
        if (null !== parse_url($linkUrl, PHP_URL_HOST)) {
            if (null === parse_url($linkUrl, PHP_URL_SCHEME)) {
                return parse_url($this->scraperConfig->getStartingUrl(), PHP_URL_SCHEME) . ':' . $linkUrl;
            }
            return $linkUrl;
        }
        return rtrim($this->baseUri, '/') . '/' . ltrim($linkUrl, '/');
    }

    /**
     * @throws MaxUrlsCrawledException
     */
    private function crawlPage(string $url): void
    {
        $event = new BeforeCrawlEvent($url);
        $this->eventDispatcher?->dispatch($event, BeforeCrawlEvent::NAME);
        if ($event->isBlocked()) {
            return;
        }
        $crawler = $this->pantherClient->request('GET', $url);

        $pageLinks = [];
        foreach ($crawler->filter('a') as $item) {
            $linkHref = $item->getAttribute('href');
            if (empty($linkHref) || str_starts_with($linkHref, '#')) {
                continue;
            }
            $linkUrl = $this->buildUrl($linkHref);
            if (!$this->isAllowedDomain($linkUrl) || \in_array($linkUrl, $this->crawledUrls, true)) {
                continue;
            }
            $pageLinks[] = $linkUrl;
        }
        $event = new PageCrawledEvent($url, $this->pantherClient->getTitle(), $this->pantherClient->getCrawler());
        $this->eventDispatcher?->dispatch($event, PageCrawledEvent::NAME);
        if ($event->isBlocked()) {
            return;
        }
        $this->crawledUrlsCount++;
        if (null !== $this->scraperConfig->getMaxLinks() && $this->crawledUrlsCount > $this->scraperConfig->getMaxLinks()) {
            throw new MaxUrlsCrawledException(sprintf('Max links limit of %u reached', $this->scraperConfig->getMaxLinks()));
        }
        $this->urlsToCrawl = array_keys(array_flip($pageLinks));
    }

    public function getPantherClient(): Client
    {
        return $this->pantherClient;
    }

    public function getScraperConfig(): ScraperConfig
    {
        return $this->scraperConfig;
    }
}
