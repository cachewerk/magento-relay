# Magento Relay

## Installation

First, [install Relay](https://relay.so/docs/installation) as a PHP extension for your CLI and FPM environments.

Next, install the Magento module:

```bash
composer require cachewerk/magento-relay
```

Finally, activate the module. Relay won't be used until you configure Magento to do so.

```bash
bin/magento module:enable CacheWerk_Relay
bin/magento setup:upgrade
```

## Configuration

If you're not using Magento's Redis integration for caching and sessions, we recommend configuring and testing that first.

- [Use Redis for session storage](https://devdocs.magento.com/guides/v2.4/config-guide/redis/redis-session.html)
- [Use Redis for default cache](https://devdocs.magento.com/guides/v2.4/config-guide/redis/redis-pg-cache.html)

### Sessions

To use Relay as the session backend, simply set `session.redis.client` to `relay` in your `app/etc/env.php`:

```diff
'session' => [
    'save' => 'redis',
    'redis' => [
+       'client' => 'relay',
        'host' => '127.0.0.1',
        'port' => 6379,
        'database' => 2,
        // ...
    ]
],
```

### Cache Backends

Relay can be used for any Redis-enabled cache backend in your `app/etc/env.php`:

```diff
'cache' => [
    'frontend' => [
        'default' => [
            'id_prefix' => '5ac_',
-           'backend' => 'Magento\\Framework\\Cache\\Backend\\Redis',
+           'backend' => 'CacheWerk\\Relay\\Cache\\Backend\\Relay',
            'backend_options' => [
                'server' => '127.0.0.1',
                'port' => 6379,
                'database' => 0,
                // ...
            ]
        ],
        'page_cache' => [
            'id_prefix' => '5ac_',
-           'backend' => 'Magento\\Framework\\Cache\\Backend\\Redis',
+           'backend' => 'CacheWerk\\Relay\\Cache\\Backend\\Relay',
            'backend_options' => [
                'server' => '127.0.0.1',
                'port' => 6379,
                'database' => 1,
                // ...
            ]
        ]
    ],
],
```
