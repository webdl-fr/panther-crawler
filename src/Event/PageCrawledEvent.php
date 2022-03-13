<?php

namespace Webdl\PantherCrawler\Event;

use Symfony\Component\Panther\DomCrawler\Crawler;

class PageCrawledEvent extends ScraperEvent
{
    public const NAME = 'crawl.page';

    public function __construct(private string $url, private string $pageTitle, private Crawler $crawler)
    {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCrawler(): Crawler
    {
        return $this->crawler;
    }

    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }
}
