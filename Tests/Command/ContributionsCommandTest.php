<?php

namespace digitalkaoz\GithubContributionsBundle\Tests\Command;


use digitalkaoz\GithubContributionsBundle\Command\ContributionsCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers digitalkaoz\GithubContributionsBundle\Command\ContributionsCommand
 */
class ContributionsCommandTest extends \PHPUnit_Framework_TestCase
{
    private $factory;
    private $command;

    public function setUp()
    {
        $this->factory = $this->getMockBuilder('digitalkaoz\GithubContributionsBundle\Factory\Contribution')
            ->disableOriginalConstructor()
            ->setMethods(array('getContributions','getUserRepos','getActivityStream','ignoreCache'))
            ->getMock()
        ;

        $this->factory->expects($this->atLeastOnce())->method('ignoreCache');

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->atLeastOnce())->method('get')->will($this->returnValue($this->factory));

        $this->command = new ContributionsCommand();
        $this->command->setContainer($container);
    }

    public function testUpdateActivity()
    {
        $this->factory->expects($this->atLeastOnce())->method('getActivityStream')->with('digitalkaoz')->will($this->returnValue(array()));
        $tester = new CommandTester($this->command);

        $tester->execute(array('cache'=>'activity', 'username'=>'digitalkaoz'));

        $this->assertEquals('activity cache for digitalkaoz successfully generated'.PHP_EOL, $tester->getDisplay());
    }

    public function testUserRepos()
    {
        $this->factory->expects($this->atLeastOnce())->method('getUserRepos')->with('digitalkaoz')->will($this->returnValue(array()));
        $tester = new CommandTester($this->command);

        $tester->execute(array('cache'=>'repos', 'username'=>'digitalkaoz'));

        $this->assertEquals('repos cache for digitalkaoz successfully generated'.PHP_EOL, $tester->getDisplay());
    }

    public function testContributions()
    {
        $this->factory->expects($this->atLeastOnce())->method('getContributions')->with('digitalkaoz')->will($this->returnValue(array()));
        $tester = new CommandTester($this->command);

        $tester->execute(array('cache'=>'contributions', 'username'=>'digitalkaoz'));

        $this->assertEquals('contributions cache for digitalkaoz successfully generated'.PHP_EOL, $tester->getDisplay());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidCache()
    {
        $tester = new CommandTester($this->command);

        $tester->execute(array('cache'=>'foo', 'username'=>'digitalkaoz'));
    }

}
 