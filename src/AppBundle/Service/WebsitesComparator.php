<?php

namespace AppBundle\Service;

use Monolog\Logger;

class WebsitesComparator
{
    /** @var CurlManager */
    private $curlManager;

    /** @var Logger */
    private $logger;

    /** @var DummyMessageApi */
    private $messageApi;

    /** @var array */
    private $loadTimes;

    /** @var string */
    private $mainWebsite;

    public function __construct(CurlManager $curlManager, Logger $logger, DummyMessageApi $dummyMessageApi)
    {
        $this->curlManager = $curlManager;
        $this->logger      = $logger;
        $this->messageApi  = $dummyMessageApi;
    }

    public function compare(array $websites)
    {
        $this->mainWebsite = $websites[0];
        $this->loadTimes   = $this->curlManager->getLoadTimes($websites);

        asort($this->loadTimes);

        $i = 0;
        $prevTime = null;
        foreach ($this->loadTimes as $website => $time)
        {
            if ($i !== 0 && $website === $this->mainWebsite)
            {
                //sendEmail

                if ($prevTime <= $time/2)
                {
                    $this->messageApi->sendMessage('Your site was twice slower!', '123456789');
                }
            }
            $i++;
            $prevTime = $time;
        }
    }

    public function prepareReport(): string
    {
        $i                = 0;
        $textResultHeader = 'FACT: ';
        $textResultBody   = '| REPORT: ';

        foreach ($this->loadTimes as $website => $time)
        {
            if ($website === $this->mainWebsite)
            {
                if ($i === 0)
                {
                    $textResultHeader .= sprintf('Main site (%s) was the fastest! :) ', $website);
                }
                else
                {
                    $textResultHeader .= sprintf('Main site (%s) was not the fastest! :( ', $website);
                }
            }

            $textResultBody .= sprintf('%d. Website (%s) load time %f ', $i+1, $website, $time);

            $i++;
        }

        //log it to log.txt then return text for command
        $this->logger->info($textResultHeader.$textResultBody);
        return $textResultHeader.$textResultBody;
    }
}