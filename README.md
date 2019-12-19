Behat PSR Extension
==========

[![CircleCI](https://circleci.com/gh/cooperaj/behat-psr-extension.svg?style=svg)](https://circleci.com/gh/cooperaj/behat-psr-extension)
[![codecov](https://codecov.io/gh/cooperaj/behat-psr-extension/branch/master/graph/badge.svg)](https://codecov.io/gh/cooperaj/behat-psr-extension)


This [Behat](http://behat.org) extension allows you to more easily test your PSR7/11/15 applications and gives you the 
ability to isolate your application components at service boundries by injecting mocks/dummies/stubs into your running 
application instances at test time. 

For instance, you may want to isolate outgoing HTTP calls from Guzzle and mock the responses of those.

## How to

For details of using this extension with Mink you can take a look at the included 
[FeatureContext.php](features/contexts/FeatureContext.php)

In essence:
1. Implement either `Psr11AwareContext` or `Psr11MinkAwareContext` in your Behat context file.
    1. `Psr11AwareContext` if you are performing non-ui based "implementation" interactions.
    2. `Psr11MinkAwareContext` if you want to utilise Mink to do browser based "acceptance" interactions.
2. If using `Psr11MinkAwareContext` a trait is provided (`Acpr\Behat\Psr\Context\RuntimeMinkContext`) that implements
    the necessary Behat injection methods and behaviours.
    