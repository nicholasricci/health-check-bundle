# Health Check Bundle

A bundle that provides a simple `/healthcheck` route

[![Latest Stable Version](https://poser.pugx.org/ekreative/health-check-bundle/v/stable.png)](https://packagist.org/packages/ekreative/health-check-bundle)
[![License](https://poser.pugx.org/ekreative/health-check-bundle/license.png)](https://packagist.org/packages/ekreative/health-check-bundle)
[![Build Status](https://api.travis-ci.com/nicholasricci/health-check-bundle.svg?branch=sf-2.8)](https://travis-ci.org/nicholasricci/health-check-bundle)

## Install

### Composer

To use this fork you need to add to your `composer.json` file this repository:
```json
{
  ...
  "repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/nicholasricci/health-check-bundle"
    }
  ],
  ...
}
```

```bash
composer require ekreative/health-check-bundle:0.2.*
```

### AppKernel

Include the bundle in your AppKernel

```php
public function registerBundles()
{
    $bundles = [
        ...
        new Ekreative\HealthCheckBundle\EkreativeHealthCheckBundle(),
```

### Routing

```yaml
ekreative_health_check:
    resource: "@EkreativeHealthCheckBundle/Controller/"
    type:     annotation
    prefix:   /
```

### Security

You should make sure `/healthcheck` does not require authentication

```yaml
security:
    firewalls:
        healthcheck:
            pattern: ^/healthcheck
            security: false
```

## Configuration

By default healthcheck will check that your default doctrine connection is working.

### Doctrine

To check more than one doctrine connection you should add the configuration, listing
the names of the connections

```yaml
ekreative_health_check:
    doctrine:
        - 'default'
        - 'alternative'
```

You can also list doctrine connections that should be checked, but don't cause a failure

```yaml
ekreative_health_check:
    optional_doctrine:
        - 'another.optional'
```

Its possible to disable the doctrine check

```yaml
ekreative_health_check:
    doctrine_enabled: false
```

#### Timeout

Its recommended to change the default PDO connection timeout so that your health
check will fail faster

Add this under connection setting

```yaml
doctrine:
    dbal:
        connections:
            default:
                driver: pdo_mysql
                host: '%database_host%'
                options:
                    !php/const PDO::ATTR_TIMEOUT: 5
```

### Redis

The bundle can also check that redis connections are working. You should add a list of
service names to check

```yaml
ekreative_health_check:
    redis:
        - 'redis'
    predis:
        - 'predis'
```

You can also list redis connections that should be checked, but don't cause a failure

```yaml
ekreative_health_check:
    optional_redis:
        - 'redis.optional'
    optional_predis:
        - 'predis.optional'
```

#### Timeout

Its recommended to change the default Redis connection timeout so that your health
check will fail faster. Its the third argument to `connect` call for `\Redis`.

```yaml
services:
    redis:
        class: Redis
        calls:
            - [ connect, ['%redis_host%', '%redis_port%', 5]]
```

#### Redis

When you want redis to be optional, you might want to use the provided `RedisFactory`
(or your own) that catches any exceptions on connect. Without this a Redis failure will
cause the container to fail, resulting in a 500 error. 

```yaml
services:
    redis:
        class: Redis
        factory: Ekreative\HealthCheckBundle\DependencyInjection\RedisFactory::get
        arguments:
            $host: 'example.com'
```

If you don't want to use Redis C extension, you can use Predis client. Is the same of Redis configuration
but with some adjustment:

```yaml
services:
    predis:
        class: Predis\Client
        factory: Ekreative\HealthCheckBundle\DependencyInjection\PredisFactory::get
        arguments:
          $parameters: 'example.com:6379'
```