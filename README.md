GithubContributionsBundle
=========================

This Bundle lets you display some Github Statistics in your Symfony2 Application.
See https://help.github.com/articles/viewing-contributions for more Information.


Installation
============

Include the Bundle in `Composer`
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

Activate the Bundle in your Kernel
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

Include `cal-heatmap` and `d3.js` and our own `contributions.js` Stylesheets and Scripts
----------------------------------------------------------

```html
    <link rel="stylesheet" href="/path/to/cal-heatmap.css" />
    <script type="text/javascript" src="/path/to/d3.min.js"></script>
    <script type="text/javascript" src="/path/to/cal-heatmap.min.js"></script>
    <script type="text/javascript" src="/bundles/digitalkaozgithubcontributions/js/contributions.js"></script>
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

* visit `/github/contributions` to view a list of repositories
* visit `/github/repos` to view a list of your own repositories
* visit `/github/activity` to view your contribution calendar (like the github one)

Generate the Caches
-------------------

the calculation of your contributions may take a while, so i build some commands to generate the caches eagerly from your console/cronjobs.

* run `app/console github:update-contributions` to update your contributions cache
* run `app/console github:update-repos` to update your repos cache
* run `app/console github:update-activity` to update your activity cache


Tests
=====

everything is well tested and under CI:

