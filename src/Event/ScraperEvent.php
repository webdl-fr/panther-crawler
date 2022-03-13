<?php

namespace Webdl\PantherCrawler\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ScraperEvent extends Event
{
    protected bool $isBlocked = false;

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function setBlocked(bool $isBlocked): void
    {
        $this->isBlocked = $isBlocked;
    }
}
