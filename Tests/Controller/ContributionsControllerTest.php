<?php

namespace digitalkaoz\GithubContributionsBundle\Tests\Controller;

use digitalkaoz\GithubContributionsBundle\Controller\ContributionsController;

/**
 * @covers digitalkaoz\GithubContributionsBundle\Controller\ContributionsController
 */
class ContributionsControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContributionsController
     */
    private $controller;
    private $factory;
    private $templating;

    public function setUp()
    {
        $templates = array(
            'contributions'   => 'FooBundle:Bar:contributions.html.twig',
            'user_repos'      => 'FooBundle:Bar:repos.html.twig',
            'activity_stream' => 'FooBundle:Bar:activity.html.twig'
        );

        $this->templating = $this->getMock('Symfony\Component\Templating\EngineInterface');
        $this->factory = $this->getMockBuilder('digitalkaoz\GithubContributionsBundle\Factory\Contribution')
            ->disableOriginalConstructor()
            ->setMethods(array('getContributions', 'getUserRepos', 'getActivityStream'))
            ->getMock();

        $this->controller = new ContributionsController($this->factory, $this->templating, $templates);
    }

    public function testContributionsAction()
    {
        $this->factory->expects($this->atLeastOnce())->method('getContributions')->with('digitalkaoz')->will($this->returnValue(array('a')));
        $this->templating->expects($this->atLeastOnce())->method('render')->with('FooBundle:Bar:contributions.html.twig', array('contributions' => array('a')))->will($this->returnValue('contributions'));

        $result = $this->controller->contributionsAction('digitalkaoz');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        $this->assertEquals('contributions', $result->getContent());
        $this->assertLessThan(400, $result->getStatusCode());
    }

    public function testUserReposAction()
    {
        $this->factory->expects($this->atLeastOnce())->method('getUserRepos')->with('digitalkaoz')->will($this->returnValue(array(array('pushed_at'=>time()), array('pushed_at'=>time()))));
        $this->templating->expects($this->atLeastOnce())->method('render')->with('FooBundle:Bar:repos.html.twig', array('repos' => array(array('pushed_at'=>time()), array('pushed_at'=>time()))))->will($this->returnValue('repos'));

        $result = $this->controller->userReposAction('digitalkaoz');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        $this->assertEquals('repos', $result->getContent());
        $this->assertLessThan(400, $result->getStatusCode());
    }

    public function testActivityStreamAction()
    {
        $data = json_decode('[["2012/11/01",2],["2012/11/02",1],["2012/11/03",0]]', true);
        $this->factory->expects($this->atLeastOnce())->method('getActivityStream')->with('digitalkaoz')->will($this->returnValue($data));
        $this->templating->expects($this->atLeastOnce())->method('render')->with('FooBundle:Bar:activity.html.twig', array('data' => array(strtotime("2012/11/01") => 2,strtotime("2012/11/02") => 1,strtotime("2012/11/03") => 0), 'min' => strtotime("2012/11/01"), 'max' => time()))->will($this->returnValue('activity'));

        $result = $this->controller->activityStreamAction('digitalkaoz');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        $this->assertEquals('activity', $result->getContent());
        $this->assertLessThan(400, $result->getStatusCode());
    }

}
