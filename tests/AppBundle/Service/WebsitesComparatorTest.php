<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\WebsitesComparator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebsitesComparatorTest extends WebTestCase
{
    /** @var WebsitesComparator */
    private static $websiteComparator;

    public static function setUpBeforeClass()
    {
        $kernel = self::createKernel();
        $kernel->boot();

        self::$websiteComparator = $kernel->getContainer()->get('websites.comparator');
    }

    //check if first one is not the fastest
    public function testCompareWithPerfectData()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $sites = [
            'http://www.telegraph.co.uk/', //its really slow
            'https://onet.pl',
            'www.google.pl',
            'codepack.pl'
        ];

        self::$websiteComparator->compare($sites);
        $report = self::$websiteComparator->getReport();


        $this->assertNotEquals(false, strpos($report, 'not'));
    }
}
