<?php

namespace digitalkaoz\GithubContributionsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('digitalkaoz_github_contributions');

        $rootNode
            ->children()
                ->scalarNode('api_token')->end()
                ->scalarNode('username')->end()
                ->scalarNode('cache_service')->end()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('contributions')->defaultValue('digitalkaozGithubContributionsBundle:Contributions:contributions.html.twig')->end()
                        ->scalarNode('activity_stream')->defaultValue('digitalkaozGithubContributionsBundle:Contributions:activity.html.twig')->end()
                        ->scalarNode('user_repos')->defaultValue('digitalkaozGithubContributionsBundle:Contributions:user_repos.html.twig')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
