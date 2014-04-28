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
     * @var bool
     */
    private $ignoreCache = false;

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
        $cacheKey = self::CONTRIBUTIONS_CACHE_KEY . $user;

        if ($data = $this->checkForCache($cacheKey)) {

            return $data;
        }

        $repos = array_filter($this->client->api('user')->setPerPage(100)->repositories($user), function ($repo) {
            return false !== $repo['fork'];
        });

        foreach ($repos as $key => $repo) {
            $details = $this->client->api('repo')->show($user, $repo['name']);

            if($this->isContributor($user, $details['parent'])) {
                $repos[$key] = $details['parent'];
            } else {
                unset($repos[$key]);
            }
        }

        $this->storeCache($cacheKey, $repos);

        return $repos;
    }

    /**
     * get all own repos from the user
     *
     * @param $user
     * @return array
     */
    public function getUserRepos($user)
    {
        $cacheKey = self::OWN_REPOS_CACHE_KEY . $user;

        if ($data = $this->checkForCache($cacheKey)) {

            return $data;
        }

        $repos = array_filter($this->client->api('user')->setPerPage(100)->repositories($user), function ($repo) {
            return false === $repo['fork'];
        });

        $this->storeCache($cacheKey, $repos);

        return $repos;
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
        $cacheKey = self::ACTIVITY_CACHE_KEY . $user;

        if ($data = $this->checkForCache($cacheKey)) {

            return $data;
        }

        $client = $this->client->getHttpClient();
        $data = json_decode($client->get('https://github.com/users/' . $user . '/contributions_calendar_data')->getBody(true), JSON_OBJECT_AS_ARRAY);

        $this->storeCache($cacheKey, $data);

        return $data;
    }

    /**
     * authenticates the api user if a token is provided (recommend for avoiding api-rate-limits)
     */
    private function authenticate()
    {
        $this->client->authenticate($this->token, null, Client::AUTH_HTTP_TOKEN);
    }

    /**
     * checks if the data is found for this cache-key
     *
     * @param string $key
     * @return mixed
     */
    private function checkForCache($key)
    {
        if ($this->cache && !$this->ignoreCache && (false !== $data = $this->cache->fetch($key))) {

            return $data;
        }
    }

    /**
     * stores data in the cache if possible
     *
     * @param string $key
     * @param mixed  $data
     */
    private function storeCache($key, $data)
    {
        if ($this->cache) {
            $this->cache->save($key, $data);
        }
    }

    /**
     * ignores the reading from cache
     */
    public function ignoreCache()
    {
        $this->ignoreCache = true;
    }

    /**
     * get the repo contributors
     *
     * @param array $repo
     * @return array
     */
    private function getContributorsFromRepo($repo)
    {
        $name = explode('/', $repo['full_name']);

        return $this->client->api('repo')->contributors($name[0], $name[1]);
    }

    /**
     * checks if a user is in the contributors list
     *
     * @param string $user
     * @param array $repo
     * @return bool
     */
    private function isContributor($user, $repo)
    {
        $contributors = $this->getContributorsFromRepo($repo);

        foreach ($contributors as $contributor) {
            if ($contributor['login'] == $user) {
                return true;
            }
        }

        return false;
    }
}
