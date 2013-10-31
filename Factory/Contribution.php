<?php

namespace digitalkaoz\GithubContributionsBundle\Factory;


use Doctrine\Common\Cache\Cache;
use Github\Client;

/**
 * Github Contributions Factory
 *
 * @author Robert Schönthal <robert.schoenthal@gmail.com>
 */
class Contribution
{
    const CONTRIBUTIONS_CACHE_KEY = 'github_contributions';
    const OWN_REPOS_CACHE_KEY = 'own_github_repos';
    const ACTIVITY_CACHE_KEY = 'github_activity';

    /**
     * @var \Github\Client
     */
    private $client;
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;
    /**
     * @var string
     */
    private $token;

    /**
     * constructor
     *
     * @param Client $client
     * @param Cache  $cache
     * @param string $token
     */
    public function __construct(Client $client, Cache $cache = null, $token = null)
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->token = $token;

        if ($this->token) {
            $this->authenticate();
        }
    }

    /**
     * get all repositories which the user contributed to
     *
     * @param $user
     * @return array
     */
    public function getContributions($user)
    {
        if ($this->cache && (false !== $data = $this->cache->fetch(self::CONTRIBUTIONS_CACHE_KEY))) {

            return $data;
        }

        $repos = $this->client->api('user')->setPerPage(100)->repositories($user);
        $contributions = array();

        foreach ($repos as $repo) {
            if (false === $repo['fork']) {
                continue;
            }

            $details = $this->client->api('repo')->show($user, $repo['name']);
            $parent = explode('/', $details['parent']['full_name']);
            $contributors = $this->client->api('repo')->contributors($parent[0], $parent[1]);

            foreach ($contributors as $contributor) {
                if ($contributor['login'] == $user) {
                    $contributions[] = $details['parent'];
                    break;
                }
            }
        }

        if ($this->cache) {
            $this->cache->save(self::CONTRIBUTIONS_CACHE_KEY, $contributions);
        }

        return $contributions;
    }

    /**
     * get all own repos from the user
     *
     * @param $user
     * @return array
     */
    public function getUserRepos($user)
    {
        if ($this->cache && (false !== $data = $this->cache->fetch(self::OWN_REPOS_CACHE_KEY))) {

            return $data;
        }

        $repos = $this->client->api('user')->setPerPage(100)->repositories($user);
        $contributions = array();

        foreach ($repos as $repo) {
            if (false === $repo['fork']) {
                $contributions[] = $repo;
            }
        }

        if ($this->cache) {
            $this->cache->save(self::OWN_REPOS_CACHE_KEY, $contributions);
        }

        return $contributions;
    }

    /**
     * returns the github activity stream (https://github.com/users/digitalkaoz/contributions_calendar_data)
     * why is it not available as API call?
     *
     * @param $user
     * @return array
     */
    public function getActivityStream($user)
    {
        if ($this->cache && (false !== $data = $this->cache->fetch(self::ACTIVITY_CACHE_KEY))) {

            return $data;
        }

        $client = clone $this->client->getHttpClient();
        $client->setOption('base_url', 'https://github.com/');
        $data = $client->get('users/' . $user . '/contributions_calendar_data')->getContent();

        if ($this->cache) {
            $this->cache->save(self::ACTIVITY_CACHE_KEY, $data);
        }

        return $data;
    }

    /**
     * authenticates the api user if a token is provided (recommend for avoiding api-rate-limits)
     */
    private function authenticate()
    {
        $this->client->authenticate($this->token, null, Client::AUTH_HTTP_TOKEN);
    }

}
