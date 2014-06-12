<?php

namespace digitalkaoz\GithubContributionsBundle\Command;

use digitalkaoz\GithubContributionsBundle\Factory\Contribution;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * command for generating contribution caches
 *
 * @author Robert Schönthal <robert.schoenthal@gmail.com>
 */
class ContributionsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('github:contribution-update');
        $this->setDescription('updates some github statistic caches');
        $this->setDefinition(array(
            new InputArgument('cache', InputArgument::REQUIRED, 'which cache?'),
            new InputArgument('username', InputArgument::REQUIRED, 'which user?')
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $factory = $this->getContainer()->get('digitalkaoz_github_contributions.factory');
        $factory->ignoreCache();

        switch ($input->getArgument('cache')) {
            case 'activity' :
                $this->updateActivityCache($input, $output, $factory);
                break;
            case 'repos' :
                $this->updateReposCache($input, $output, $factory);
                break;
            case 'contributions' :
                $this->updateContributionsCache($input, $output, $factory);
                break;
            default:
                throw new \InvalidArgumentException('invalid "cache" "'.$input->getArgument('cache').'" given, must be one of [activity|repos|contributions]');
        }
    }

    /**
     * updates the activity cache
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Contribution    $factory
     */
    private function updateActivityCache(InputInterface $input, OutputInterface $output, Contribution $factory)
    {
        $factory->getActivityStream($input->getArgument('username'));
        $output->writeln('activity cache for <info>' . $input->getArgument('username') . '</info> successfully generated');
    }

    /**
     * updates the user repo cache
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Contribution    $factory
     */
    private function updateReposCache(InputInterface $input, OutputInterface $output, Contribution $factory)
    {
        $factory->getUserRepos($input->getArgument('username'));
        $output->writeln('repos cache for <info>'.$input->getArgument('username').'</info> successfully generated');
    }

    /**
     * updates the contribution cache
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Contribution    $factory
     */
    private function updateContributionsCache(InputInterface $input, OutputInterface $output, Contribution $factory)
    {
        $factory->getContributions($input->getArgument('username'));
        $output->writeln('contributions cache for <info>'.$input->getArgument('username').'</info> successfully generated');
    }
}
