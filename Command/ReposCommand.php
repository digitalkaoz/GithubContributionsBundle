<?php

namespace digitalkaoz\GithubContributionsBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * command for generating user repo caches
 *
 * @author Robert Schönthal <robert.schoenthal@gmail.com>
 */
class ReposCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('github:update-repos');
        $this->setDescription('updates your github repos');
        $this->addArgument(
            'username', InputArgument::REQUIRED, 'for which user fetch the repos?'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $factory = $this->getContainer()->get('digitalkaoz_github_contributions.factory');

        $factory->getUserRepos($input->getArgument('username'));
    }
}