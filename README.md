Behat PSR Extension
==========

[![CircleCI](https://circleci.com/gh/cooperaj/behat-psr-extension.svg?style=svg)](https://circleci.com/gh/cooperaj/behat-psr-extension)
[![codecov](https://codecov.io/gh/cooperaj/behat-psr-extension/branch/master/graph/badge.svg)](https://codecov.io/gh/cooperaj/behat-psr-extension)

This [Behat](http://behat.org) extension allows you to more easily test your [PSR7](https://www.php-fig.org/psr/psr-7/) 
/ [11](https://www.php-fig.org/psr/psr-11/) / [15](https://www.php-fig.org/psr/psr-15/) applications and gives you the 
ability to isolate your application components at service boundaries by injecting mocks/dummies/stubs into your running 
application instances at test time. 

For instance, you may want to isolate outgoing HTTP calls from Guzzle and mock the responses of those. This is sometimes
called *whitebox* testing.

## Implementation

This builds on the work of [@ciaranmcnulty](https://github.com/ciaranmcnulty) and his [behat-psr7extension](https://github.com/ciaranmcnulty/behat-psr7extension)
by injecting the built PSR7 application into the behat contexts alongside the PSR11 Container that is responsible for
it. When this happens you're able to modify the running application by manipulation of the Container contents. 

An example of this is the replacement of a AWS handler with a AWS mock handler, which allows you to mock responses to 
AWS requests as a part of your context steps definitions.

## How to

For details of using this extension with Mink you can take a look at the included 
[FeatureContext.php](features/contexts/FeatureContext.php)

In essence:
1. Implement either `Psr11AwareContext` or `Psr11MinkAwareContext` in your Behat context file.
    1. `Psr11AwareContext` if you are performing non-ui based "implementation" interactions.
    2. `Psr11MinkAwareContext` if you want to utilise Mink to do browser based "acceptance" interactions.
2. If using `Psr11MinkAwareContext` a trait is provided (`Acpr\Behat\Psr\Context\RuntimeMinkContext`) that implements
    the necessary Behat injection methods and behaviours.
    