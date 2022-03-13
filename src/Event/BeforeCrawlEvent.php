<?php

namespace Webdl\PantherCrawler\Event;

class BeforeCrawlEvent extends ScraperEvent
{
    public const NAME = 'crawl.before';

    public function __construct(private string $url)
    {
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
