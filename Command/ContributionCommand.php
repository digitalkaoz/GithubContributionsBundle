<?php

namespace digitalkaoz\GithubContributionsBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * command for generating contribution caches
 *
 * @author Robert Schönthal <robert.schoenthal@gmail.com>
 */
class ContributionCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('github:update-contributions');
        $this->setDescription('updates your github contributions');
        $this->addArgument(
            'username', InputArgument::REQUIRED, 'for which user fetch the contributions?'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $factory = $this->getContainer()->get('digitalkaoz_github_contributions.factory');

        $factory->getContributions($input->getArgument('username'));
    }
}