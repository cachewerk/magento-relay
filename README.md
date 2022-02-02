# Magento Relay

## Installation

```bash
composer require cachewerk/magento-relay
```

## Sessions

https://devdocs.magento.com/guides/v2.4/config-guide/redis/redis-session.html

```bash
bin/magento setup:config:set --session-save=redis
bin/magento setup:config:set --session-save-redis-client=relay
```

## Default Cache

https://devdocs.magento.com/guides/v2.4/config-guide/redis/redis-pg-cache.html

```bash
bin/magento setup:config:set --cache-backend=CacheWerk\\Relay\\Cache\\Backend\\Relay
```

## Page Cache

https://devdocs.magento.com/guides/v2.4/config-guide/redis/redis-pg-cache.html

```bash
bin/magento setup:config:set --page-cache=CacheWerk\\Relay\\Cache\\Backend\\Relay
```
