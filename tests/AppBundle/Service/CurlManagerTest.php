<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\CurlManager;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class CurlManagerTest extends TestCase
{
    public function testLoadTimes()
    {
        $sites =
            [
                'www.google.pl',
                'codepack.pl',
            ];

        $loadTimes = (new CurlManager)->getLoadTimes($sites);

        $this->assertArrayHasKey('www.google.pl', $loadTimes);
        $this->assertArrayHasKey('codepack.pl', $loadTimes);

    }

    public function testGetWebsiteStatusWithPerfectData()
    {
        $site = 'www.google.pl';

        $status = (new CurlManager)->getWebsiteStatus($site);

        $this->assertEquals(200, $status);
    }

    public function testGetWebsiteStatusWithBadData()
    {
        $site = 'google.pl';

        $status = (new CurlManager)->getWebsiteStatus($site);

        $this->assertNotEquals(200, $status);
    }
}
