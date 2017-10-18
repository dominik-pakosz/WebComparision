<?php

declare(strict_types=1);

namespace AppBundle\Service;

class CurlManager
{
    public function getLoadTimes(array $websites): array
    {
        $result = [];

        foreach ($websites as $website)
        {
            $ch = curl_init($website);
            //mute output of the curl
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);

            if (!curl_errno($ch))
            {
                $result[$website] = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
            }

            curl_close($ch);
        }

        return $result;
    }

    public function getWebsiteStatus(string $website): int
    {
        $ch = curl_init($website);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpcode;
    }
}