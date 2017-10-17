<?php

namespace AppBundle\Command;

use AppBundle\Exception\InvalidNumberOfWebsitesException;
use AppBundle\Exception\WebsiteDidNotRespondException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebsitesLoadTimeCompareCommand extends ContainerAwareCommand
{
    const WEBSITE_ARGUMENT = 'websites';

    protected function configure()
    {
        $this
            ->setName('app:websites-compare')
            ->setDescription('Check which website loads faster')
            ->addArgument(
                self::WEBSITE_ARGUMENT,
                InputArgument::IS_ARRAY,
                'First argument given will be compared to next arguments (separate websites with space)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $websites = $input->getArgument(self::WEBSITE_ARGUMENT);

        try
        {
            $this->getContainer()->get('websites.checker')->check($websites);
        }catch (InvalidNumberOfWebsitesException $exception)
        {
            //message: Więcej niż 1!
        }catch (WebsiteDidNotRespondException $exception)
        {
            //$exception->getMessage();//url
            //$exception->getCode();//kod błędu
        }

        $websiteComparator = $this->getContainer()->get('websites.comparator');
        $websiteComparator->compare($websites);
        $result = $websiteComparator->prepareReport();

    }
}
