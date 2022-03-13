<?php

namespace Webdl\PantherCrawler\Event;

class AfterCrawlEvent extends ScraperEvent
{
    public const NAME = 'crawl.after';

    public function __construct(private string $url, private array $pageLinks = [])
    {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPageLinks(): array
    {
        return $this->pageLinks;
    }
}
