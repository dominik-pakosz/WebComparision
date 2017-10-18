<?php

namespace Tests\AppBundle\Service;

use AppBundle\Exception\InvalidNumberOfWebsitesException;
use AppBundle\Exception\WebsiteDidNotRespondException;
use AppBundle\Service\WebsitesChecker;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebsitesCheckerTest extends WebTestCase
{
    /** @var WebsitesChecker */
    private static $websiteChecker;

    public static function setUpBeforeClass()
    {
        $kernel = self::createKernel();
        $kernel->boot();

        self::$websiteChecker = $kernel->getContainer()->get('websites.checker');
    }

    public function testCheckWithBadArgumentsNumber()
    {
        $this->expectException(InvalidNumberOfWebsitesException::class);

        $sites =
            [
                'codepack.pl',
            ];

        self::$websiteChecker->check($sites);
    }

    public function testCheckWithBadWebsiteUrl()
    {
        $this->expectException(WebsiteDidNotRespondException::class);

        $sites =
            [
                'codepack.pl',
                'www.google.pl',
                'onet.pl' //bad
            ];

        self::$websiteChecker->check($sites);
    }
}
