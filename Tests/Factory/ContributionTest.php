<?php
/**
 * Created by PhpStorm.
 * User: caziel
 * Date: 01.11.13
 * Time: 16:41
 */

namespace digitalkaoz\GithubContributionsBundle\Tests\Factory;

use digitalkaoz\GithubContributionsBundle\Factory\Contribution;
use Github\Client;
use Guzzle\Http\Message\Response;

/**
 * @covers digitalkaoz\GithubContributionsBundle\Factory\Contribution
 */
class ContributionTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    private $cache;

    /**
     * @var Contribution
     */
    private $factory;

    public function setUp()
    {
        $token = 'github_api_token';
        $this->cache = $this->getMock('Doctrine\Common\Cache\Cache');
        $this->client = $this->getMock('Github\HttpClient\HttpClientInterface');
        $githubClient = new Client($this->client);
        $this->factory = new Contribution($githubClient, $this->cache, $token);

    }

    public function testGetContributions()
    {
        $this->cache->expects($this->atLeastOnce())->method('fetch');
        $this->cache->expects($this->atLeastOnce())->method('save');

        //1. fetch all own repos
        $responseRepos = new Response(200);
        $responseRepos->setBody('[
                {
                  "name": "parent",
                  "full_name": "foo/parent",
                  "owner": {
                    "login": "foo"
                  },
                  "fork": true
                },
                {
                  "name": "yuml-php",
                  "full_name": "foo/yuml-php",
                  "owner": {
                    "login": "foo"
                  },
                  "fork": false
                }
        ]');

        //2. fetch detail for a repo to get the parent
        $responseOwnRepo = new Response(200);
        $responseOwnRepo->setBody('
                {
                  "name": "parent",
                  "full_name": "foo/parent",
                  "owner": {
                    "login": "foo"
                  },
                  "fork": true,
                  "parent": {
                      "name": "parent",
                      "full_name": "bar/parent",
                      "owner": {
                        "login": "bar"
                      }
                  }
                }
        ');

        //3. fetch the contributors from the parent
        $responseContributors = new Response(200);
        $responseContributors->setBody('[
                {
                    "login": "foo"
                },
                {
                    "login": "bar"
                }
        ]');

        $this->client->expects($this->any())->method('get')->will($this->returnValueMap(array(
            array('users/foo/repos', array('per_page' => 100), array(), $responseRepos),
            array('repos/foo/parent', array(), array(), $responseOwnRepo),
            array('repos/bar/parent/contributors', array('anon' => null), array(), $responseContributors)
        )));
        $result = $this->factory->getContributions('foo');

        //we expect only the "bar/parent" repo
        $this->assertEquals(json_decode('[{
            "name": "parent",
            "full_name": "bar/parent",
            "owner": {
                "login": "bar"
            }
        }]', true), $result);
    }

    public function testGetContributionsCached()
    {
        $data = json_decode('[{
            "name": "parent",
            "full_name": "bar/parent",
            "owner": {
                "login": "bar"
            }
        }]', true);

        $this->client->expects($this->never())->method('get');
        $this->cache->expects($this->atLeastOnce())->method('fetch')->with(Contribution::CONTRIBUTIONS_CACHE_KEY . 'foo')->will($this->returnValue($data));

        $result = $this->factory->getContributions('foo');

        $this->assertEquals($data, $result);
    }

    public function testGetActivityStream()
    {
        $data = '[["2012/11/01",2],["2012/11/02",1],["2012/11/03",0]]';
        $response = new Response(200);
        $response->setBody($data);

        $this->client->expects($this->atLeastOnce())->method('get')->with('users/foo/contributions_calendar_data')->will($this->returnValue($response));
        $result = $this->factory->getActivityStream('foo');

        $this->assertEquals(json_decode($data, true), $result);
    }

    public function testGetActivityStreamCached()
    {
        $data = json_decode('[["2012/11/01",2],["2012/11/02",1],["2012/11/03",0]]', true);

        $this->client->expects($this->never())->method('get');
        $this->cache->expects($this->atLeastOnce())->method('fetch')->with(Contribution::ACTIVITY_CACHE_KEY . 'foo')->will($this->returnValue($data));

        $result = $this->factory->getActivityStream('foo');

        $this->assertEquals($data, $result);
    }

    public function testGetUserRepos()
    {
        $data = '[
                {
                  "name": "yuml-php",
                  "full_name": "digitalkaoz/yuml-php",
                  "owner": {
                    "login": "digitalkaoz"
                  },
                  "fork": false
                }
        ]';
        $response = new Response(200);
        $response->setBody($data);

        $this->client->expects($this->atLeastOnce())->method('get')->with('users/foo/repos')->will($this->returnValue($response));
        $result = $this->factory->getUserRepos('foo');

        $this->assertEquals(json_decode($data, true), $result);
    }

    public function testGetUserReposCached()
    {
        $data = json_decode('[
                {
                  "name": "yuml-php",
                  "full_name": "digitalkaoz/yuml-php",
                  "owner": {
                    "login": "digitalkaoz"
                  },
                  "fork": false
                }
        ]', true);

        $this->client->expects($this->never())->method('get');
        $this->cache->expects($this->atLeastOnce())->method('fetch')->with(Contribution::OWN_REPOS_CACHE_KEY . 'foo')->will($this->returnValue($data));

        $result = $this->factory->getUserRepos('foo');

        $this->assertEquals($data, $result);
    }

    public function testIgnoreCache()
    {
        $data = '[["2012/11/01",2],["2012/11/02",1],["2012/11/03",0]]';

        $this->cache->expects($this->never())->method('fetch');
        $this->factory->ignoreCache();

        $response = new Response(200);
        $response->setBody($data);

        $this->client->expects($this->atLeastOnce())->method('get')->with('users/foo/contributions_calendar_data')->will($this->returnValue($response));
        $result = $this->factory->getActivityStream('foo');

        $this->assertEquals(json_decode($data, true), $result);
    }

}
 