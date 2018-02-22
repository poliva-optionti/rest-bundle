MNC RestBundle
==============
Provides some utilties to rapidly build Restful API's.

> This library is not ready for release yet. Do not use in production!

You can check the documentation [here](/src/Resources/docs/0.intro.md), but frist
familiarize yourself with this readme.

## Features:
- Create RESTful endpoints in seconds with our awesome RestController
- Json-Schema forms by `limenius/liform`
- Entity Transformers by `league/fractal`
- Eager Load selectable Hydration
- Pagination at ORM level by `whiteoctober/pagerfanta`
- Securing your entitites implementing `OwnableInterface`
- Json Body Parser
- RFC 7807 Problem Details implementation for errors.

## Roadmap:
- Hypermedia Links Manager
- Advanced Collection Filtering
- Subresource Route Support
- Object Managers
- Content Negotiation

## Install:

Simply run:

    composer require mnavarrocarter/rest-bundle
   
Then register the bundle in your kernel:

    // app/AppKernel.php
    
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
    
                new MNC\RestBundle\MNCRestBundle(),
            );
    
            // ...
        }
    
        // ...
    } 

## Configuration

### Symfony Translator
There's an issue using Symfony 4.0. Before using this package you need to install the translation
component. You can do this with:

    composer req translator

Flex will take care of everything.

### Route Loading
There's no need for a custom route loader like Knp's RestBundle. Simply load your
Api Controllers with the following config, and your routes will have consistent names:

    api:
        resource: "@ApiBundle/Controller/"
        prefix: /api
        defaults:
            _format: json

### Access Desition Manager
If you are going to extend the `RestController` in your controllers, then you should
now that it uses Symfony Security Voters to grant/deny access to entities implementing our
`OwnableInterface`. However, in entities not implementing that interface, this will
cause an access denied due to the Symfony's Access Desition Manager default config.

To solve this, we recommend you the following configuration in your security config:

    // config/security.yml
    
    security:
        
        #...
        
        access_decision_manager:
            strategy: unanimous
            allow_if_all_abstain: true

## Usage
To get a deep understanding on how this bundle works, read [the docs](/src/Resources/docs/0.intro.md).

## Credits
This bundle incorporates services definitions and code from these other bundles that
were extracted here to avoid dependency in other bundles.
- `limenius/liform-bundle`
- `samjarret/fractal-bundle`
