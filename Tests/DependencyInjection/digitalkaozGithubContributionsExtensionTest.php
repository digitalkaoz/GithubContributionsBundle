<?php

namespace digitalkaoz\GithubContributionsBundle\Tests\DependencyInjection;

use digitalkaoz\GithubContributionsBundle\DependencyInjection\digitalkaozGithubContributionsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers digitalkaoz\GithubContributionsBundle\DependencyInjection\digitalkaozGithubContributionsExtension
 * @covers digitalkaoz\GithubContributionsBundle\DependencyInjection\Configuration
 */
class digitalkaozGithubContributionsExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $container = new ContainerBuilder();
        $extension = new digitalkaozGithubContributionsExtension();
        $extension->load(array(array('username' => 'foo', 'cache_service'=>'foo_cache_service','api_token'=>'github_api_token')), $container);

        $this->assertTrue($container->hasDefinition('digitalkaoz_github_contributions.controller'));
        $this->assertTrue($container->hasDefinition('digitalkaoz_github_contributions.factory'));

        $this->assertEquals(array(
            'contributions'   => "digitalkaozGithubContributionsBundle:Contributions:contributions.html.twig",
            'activity_stream' => "digitalkaozGithubContributionsBundle:Contributions:activity.html.twig",
            'user_repos'      => "digitalkaozGithubContributionsBundle:Contributions:user_repos.html.twig"
        ), $container->getDefinition('digitalkaoz_github_contributions.controller')->getArgument(2));

        $this->assertEquals('foo', $container->getDefinition('digitalkaoz_github_contributions.controller')->getArgument(3));

        $this->assertEquals(new Reference('foo_cache_service'), $container->getDefinition('digitalkaoz_github_contributions.factory')->getArgument(1));
        $this->assertEquals('github_api_token', $container->getDefinition('digitalkaoz_github_contributions.factory')->getArgument(2));
    }
}
 