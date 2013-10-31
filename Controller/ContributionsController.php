<?php

namespace digitalkaoz\GithubContributionsBundle\Controller;

use digitalkaoz\GithubContributionsBundle\Factory\Contribution;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\Templating\EngineInterface;

/**
 * Controller for Github Contribution Statistics
 *
 * @author Robert Schönthal <robert.schoenthal@gmail.com>
 */
class ContributionsController
{
    /**
     * @var Contribution
     */
    private $factory;
    /**
     * @var TwigEngine
     */
    private $templating;
    /**
     * @var string
     */
    private $user;
    /**
     * @var array
     */
    private $templates;

    /**
     * constructor
     *
     * @param Contribution $factory
     * @param TwigEngine   $templating
     * @param array        $templates
     */
    public function __construct(Contribution $factory, EngineInterface $templating, array $templates, $user = null)
    {
        $this->factory = $factory;
        $this->templating = $templating;
        $this->templates = $templates;
        $this->user = $user;
    }

    /**
     * fetches all repositories which the user contributed to
     *
     * @param string $username
     * @return Response
     * @throws NotAcceptableHttpException
     * @see https://help.github.com/articles/viewing-contributions#repositories-contributed
     */
    public function contributionsAction($username = null)
    {
        $username = $this->preCheckUser($username);
        $contributions = $this->factory->getContributions($username);

        return new Response($this->templating->render($this->templates['contributions'], array('contributions' => $contributions)));
    }

    /**
     * fetches all repositories of this user
     *
     * @param string $username
     * @return Response
     * @throws NotAcceptableHttpException
     */
    public function userReposAction($username = null)
    {
        $username = $this->preCheckUser($username);
        $repos = $this->factory->getUserRepos($username);
        $this->sortReposByRecentPush($repos);

        return new Response($this->templating->render($this->templates['user_repos'], array('repos' => $repos)));
    }

    /**
     * fetches the activity stream
     *
     * @param string $username
     * @return Response
     * @throws NotAcceptableHttpException
     * @see https://help.github.com/articles/viewing-contributions#contributions-calendar
     */
    public function activityStreamAction($username = null)
    {
        $username = $this->preCheckUser($username);
        $data = $this->factory->getActivityStream($username);
        list($formatted, $min, $max) = $this->prepareActivityData($data);

        return new Response($this->templating->render($this->templates['activity_stream'], array('data' => $formatted, 'min' => $min, 'max' => $max)));
    }

    /**
     * checks if a user is injected or given a request parameter
     *
     * @param string $username
     * @return string
     * @throws NotAcceptableHttpException
     */
    private function preCheckUser($username = null)
    {
        if (!$username && !$this->user) {
            throw new NotAcceptableHttpException('either set username or pass a username');
        }

        return $username ?: $this->user;
    }

    /**
     * sorts repositories by recent pushed date
     *
     * @param array $repos
     */
    private function sortReposByRecentPush(array &$repos)
    {
        usort($repos, function ($a, $b) {
            if (strtotime($a['pushed_at']) == strtotime($b['pushed_at'])) {
                return 0;
            }

            return strtotime($a['pushed_at']) > strtotime($b['pushed_at']) ? -1 : 1;
        });
    }

    /**
     * prepares the activity for cal-heatmap
     *
     * @param array $data
     * @return array
     * @see http://kamisama.github.io/cal-heatmap
     */
    private function prepareActivityData(array $data)
    {
        $formatted = array();
        $min = time();
        $max = time();

        foreach ($data as $set) {
            if (strtotime($set[0]) < $min) {
                $min = strtotime($set[0]);
            }
            if (strtotime($set[0]) > $max) {
                $max = strtotime($set[0]);
            }
            $formatted[strtotime($set[0])] = $set[1];
        }

        return array($formatted, $min, $max);
    }

}
