<?php

declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Exception\InvalidNumberOfWebsitesException;
use AppBundle\Exception\WebsiteDidNotRespondException;

class WebsitesChecker
{
    /** @var CurlManager */
    private $curlManager;

    public function __construct(CurlManager $curlManager)
    {
        $this->curlManager = $curlManager;
    }

    public function check(array $websites): void
    {
        $this->checkWebsitesNumber($websites);
        $this->checkWebsitesStatuses($websites);
    }

    private function checkWebsitesNumber(array $websites)
    {
        if (count($websites) < 2)
        {
            throw new InvalidNumberOfWebsitesException();
        }
    }

    private function checkWebsitesStatuses(array $websites)
    {
        foreach ($websites as $website)
        {
            $status = $this->curlManager->getWebsiteStatus($website);

            if ($status < 200 || $status >= 300)
            {
                throw new WebsiteDidNotRespondException($website, $status);
            }
        }
    }
}