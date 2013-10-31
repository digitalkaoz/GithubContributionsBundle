GithubContributionsBundle
=========================

This Bundle lets you display some Github Statistics in your Symfony2 Application.
See https://help.github.com/articles/viewing-contributions for more Information.

[![Build Status](https://travis-ci.org/digitalkaoz/GithubContributionsBundle.png?branch=master)](https://travis-ci.org/digitalkaoz/GithubContributionsBundle)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/digitalkaoz/GithubContributionsBundle/badges/quality-score.png?s=c68ce65808d8b57755f1ec492ae1036fd94bf875)](https://scrutinizer-ci.com/g/digitalkaoz/GithubContributionsBundle/)
[![Code Coverage](https://scrutinizer-ci.com/g/digitalkaoz/GithubContributionsBundle/badges/coverage.png?s=737b1c8195155fe8aeb3bf956b04d0bd77d1d3e2)](https://scrutinizer-ci.com/g/digitalkaoz/GithubContributionsBundle/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a74d75ea-6aa5-4cf9-95dd-db4afbb5b2dc/mini.png)](https://insight.sensiolabs.com/projects/a74d75ea-6aa5-4cf9-95dd-db4afbb5b2dc)
[![Latest Stable Version](https://poser.pugx.org/digitalkaoz/github-contributions-bundle/v/stable.png)](https://packagist.org/packages/digitalkaoz/github-contributions-bundle)
[![Total Downloads](https://poser.pugx.org/digitalkaoz/github-contributions-bundle/downloads.png)](https://packagist.org/packages/digitalkaoz/github-contributions-bundle)

Installation
============

Add the Bundle with `Composer`
---------------------------

```json
{
    "require" : {
        "digitalkaoz/GithubContributionsBundle" : "dev-master"
    }
}
```

For Caching add `"liip/doctrine-cache-bundle": "~1.0"` as well!

Then update your Dependencies: `php composer.phar update`

Activate the Bundle in your `AppKernel`
----------------------------------

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new digitalkaoz\GithubContributionsBundle\digitalkaozGithubContributionsBundle()
    );

    return $bundles;
}
```

Include the Routing File in your `routing.yml`
----------------------------------------------

```yaml
digitalkaoz_github_contributions:
    resource: "@digitalkaozGithubContributionsBundle/Resources/config/routing.xml"
    prefix:   /github
```

Configuration
=============

the full configuration looks like this:

```yaml
# only needed if you want to cache the github reponses (recommend)
liip_doctrine_cache:
    namespaces:
        github:
            type: file_system

#nothing is required, but its recommended to use an api token and cache the results
digitalkaoz_github_contributions:
    api_token:     your_github_api_token
    cache_service: liip_doctrine_cache.ns.github
    username:      your_github_username
    templates:
        contributions:   digitalkaozGithubContributionsBundle:Contributions:contributions.html.twig
        activity_stream: digitalkaozGithubContributionsBundle:Contributions:activity.html.twig
        user_repos:      digitalkaozGithubContributionsBundle:Contributions:user_repos.html.twig
```

Usage
=====

View the Statistics
-------------------

* visit `/github/contributions` to view a list of repositories you have contributed to
* visit `/github/repos` to view a list of your own repositories
* visit `/github/activity` to view your contribution calendar (like the github one)

Generate the Caches
-------------------

TODO...

the calculation of your contributions may take a while, so i build some commands to generate the caches eagerly from your console/cronjobs.

* run `app/console github:update-contributions` to update your contributions cache
* run `app/console github:update-repos` to update your repos cache
* run `app/console github:update-activity` to update your activity cache


Tests
=====

everything is well tested and under CI:

