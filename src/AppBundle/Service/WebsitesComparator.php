<?php

declare(strict_types=1);

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

    /** @var \Swift_Mailer */
    private $mailer;

    /** @var array */
    private $loadTimes;

    /** @var string */
    private $mainWebsite;

    /** @var string|null */
    private $report = null;

    public function __construct(CurlManager $curlManager, Logger $logger, DummyMessageApi $dummyMessageApi, \Swift_Mailer $mailer)
    {
        $this->curlManager = $curlManager;
        $this->logger      = $logger;
        $this->messageApi  = $dummyMessageApi;
        $this->mailer      = $mailer;
    }

    public function compare(array $websites)
    {
        $this->mainWebsite = $websites[0];
        $this->loadTimes   = $this->curlManager->getLoadTimes($websites);

        asort($this->loadTimes);

        $i = 0;
        $fastestTime = 0.00;
        $mainWebsiteTime = null;

        foreach ($this->loadTimes as $website => $time)
        {
            if ($i !== 0 && $website === $this->mainWebsite)
            {
                //sendEmail
                $this->sendEmail();

                $mainWebsiteTime = $time;
            }

            if ($fastestTime > $time && $website !== $this->mainWebsite)
            {
                $fastestTime = $time;
            }

            $i++;
        }

        if ($mainWebsiteTime !== null && $fastestTime >= $mainWebsiteTime/2)
        {
               $this->messageApi->sendMessage('Your site was twice slower than fastest one!', '876123441');
        }
    }

    public function getReport(): string
    {
        if ($this->report !== null)
        {
            return $this->report;
        }

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

        $this->report = $textResultHeader.$textResultBody;
        $this->logResult($this->report);
        return $this->report;
    }

    private function sendEmail()
    {
        $report = $this->getReport();
        $message = (new \Swift_Message('Your site was slower!'))
            ->setFrom('dpakoo906@gmail.com')
            //->setTo('recipient@example.com') definied in config_dev.yml
            ->setBody(
                $report
            )
        ;
        $this->mailer->send($message);
    }

    private function logResult(string $result)
    {
        //log it to log.txt
        $this->logger->info($result);
    }
}