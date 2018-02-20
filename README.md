MNC RestBundle
==============
Provides some utilties to rapidly build Restful API's.

> This library is not ready for release yet. Do not use in production!

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
- Subresources

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
Check [the docs](/src/Resources/docs/intro.md).

## Credits
This bundle incorporates services definitions and code from these other bundles, that
were extracted to avoid dependencies in other bundles.
- `limenius/liform-bundle`
- `samjarret/fractal-bundle`