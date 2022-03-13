# panther-crawler
(very basic) Web crawler based on [Panther](https://github.com/symfony/panther)

### Installing panther-crawler

Use [Composer](https://getcomposer.org/) to install panther-crawler in your project:

    composer req webdl/panther-crawler

### Installing ChromeDriver and geckodriver

[Panther](https://github.com/symfony/panther) uses the WebDriver protocol to control the browser used to crawl websites.

On all systems, you can use [`dbrekelmans/browser-driver-installer`](https://github.com/dbrekelmans/browser-driver-installer)
to install ChromeDriver and geckodriver locally:

    composer require --dev dbrekelmans/bdi
    vendor/bin/bdi detect drivers

### Basic Usage

```php
<?php

use Symfony\Component\Panther\Client;
use Webdl\PantherCrawler\Config\ScraperConfig;
use Webdl\PantherCrawler\Scraper\Scraper;

require __DIR__.'/vendor/autoload.php'; // Composer's autoloader

$client = Client::createChromeClient();
// Or, if you care about the open web and prefer to use Firefox
$client = Client::createFirefoxClient();

// Adjust the config
$scrapperConfig = ScraperConfig::create('https://fr.wikipedia.org/', maxLinks: 200);

$crawler = new Scraper($client, $scrapperConfig);
$crawler->crawl();
```

### Basic Usage With Event Dispatching

```php
<?php

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Panther\Client;
use Webdl\PantherCrawler\Config\ScraperConfig;
use Webdl\PantherCrawler\Event\PageCrawledEvent;
use Webdl\PantherCrawler\Scraper\Scraper;

require __DIR__.'/vendor/autoload.php'; // Composer's autoloader

$eventDispatcher = new EventDispatcher();
$client = Client::createChromeClient();
// Or, if you care about the open web and prefer to use Firefox
$client = Client::createFirefoxClient();

$eventDispatcher->addListener(PageCrawledEvent::NAME, function(PageCrawledEvent $event) {
    echo 'A page was crawled!' . PHP_EOL;
});
$scrapperConfig = ScraperConfig::create('https://fr.wikipedia.org/', maxLinks: 200);
$crawler = new Scraper($client, $scrapperConfig, $eventDispatcher);
$crawler->crawl();
```
