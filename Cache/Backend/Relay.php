<?php

namespace CacheWerk\Relay\Cache\Backend;

use Zend_Cache;

use CacheWerk\Relay\Credis\CredisProxy;

use Magento\Framework\Cache\Backend\Redis;

class Relay extends Redis
{
    /**
     * The Redis client.
     *
     * @var \Relay\Relay
     */
    protected $_redis;

    /**
     * {@inheritdoc}
     */
    public function __construct($options = [])
    {
        $this->preloadKeys = $options['preload_keys'] ?? [];

        if (empty($options['server']) && empty($options['cluster'])) {
            Zend_Cache::throwException('Redis "server" not specified.');
        }

        $this->_clientOptions = $this->getClientOptions($options);

        $sentinelMaster = empty($options['sentinel_master']) ? null : $options['sentinel_master'];

        if ($sentinelMaster) {
            Zend_Cache::throwException('Relay does not support Redis Sentinel.');
        }

        if (! empty($options['cluster'])) {
            Zend_Cache::throwException('Relay does not support Redis Cluster.');
        }

        $port = isset($options['port']) ? $options['port'] : 6379;

        $relay = new \Relay\Relay;
        $relay->setOption($relay::OPT_PHPREDIS_COMPATIBILITY, true);
        $relay->setOption($relay::OPT_MAX_RETRIES, $this->_clientOptions->connectRetries);
        $relay->connect($options['server'], $port, $this->_clientOptions->timeout, 0, $this->_clientOptions->readTimeout ?: 0);

        $this->_applyClientOptions($relay);

        $this->_redis = new CredisProxy($relay);

        if (isset($options['load_from_slave'])) {
            Zend_Cache::throwException('Relay does not support the "load_from_slave" option.');
        }

        if (isset($options['notMatchingTags'])) {
            $this->_notMatchingTags = (bool) $options['notMatchingTags'];
        }

        if (isset($options['compress_tags'])) {
            $this->_compressTags = (int) $options['compress_tags'];
        }

        if (isset($options['compress_data'])) {
            $this->_compressData = (int) $options['compress_data'];
        }

        if (isset($options['lifetimelimit'])) {
            $this->_lifetimelimit = (int) min($options['lifetimelimit'], self::MAX_LIFETIME);
        }

        if (isset($options['compress_threshold'])) {
            $this->_compressThreshold = (int) $options['compress_threshold'];

            if ($this->_compressThreshold < 1) {
                $this->_compressThreshold = 1;
            }
        }

        if (isset($options['automatic_cleaning_factor'])) {
            $this->_options['automatic_cleaning_factor'] = (int) $options['automatic_cleaning_factor'];
        } else {
            $this->_options['automatic_cleaning_factor'] = 0;
        }

        if (isset($options['compression_lib'])) {
            $this->_compressionLib = (string) $options['compression_lib'];
        } elseif (function_exists('snappy_compress')) {
            $this->_compressionLib = 'snappy';
        } elseif (function_exists('lz4_compress')) {
            if (version_compare(phpversion('lz4'), "0.3.0") < 0) {
                $this->_compressTags = $this->_compressTags > 1 ? true : false;
                $this->_compressData = $this->_compressData > 1 ? true : false;
            }

            $this->_compressionLib = 'l4z';
        } elseif (function_exists('zstd_compress')) {
            if (version_compare(phpversion('zstd'), "0.4.13") < 0) {
                $this->_compressTags = $this->_compressTags > 1 ? true : false;
                $this->_compressData = $this->_compressData > 1 ? true : false;
            }

            $this->_compressionLib = 'zstd';
        } elseif (function_exists('lzf_compress')) {
            $this->_compressionLib = 'lzf';
        } else {
            $this->_compressionLib = 'gzip';
        }

        $this->_compressPrefix = substr($this->_compressionLib, 0, 2) . self::COMPRESS_PREFIX;

        if (isset($options['sunion_chunk_size']) && $options['sunion_chunk_size'] > 0) {
            $this->_sunionChunkSize = (int) $options['sunion_chunk_size'];
        }

        if (isset($options['remove_chunk_size']) && $options['remove_chunk_size'] > 0) {
            $this->_removeChunkSize = (int) $options['remove_chunk_size'];
        }

        if (isset($options['use_lua'])) {
            $this->_useLua = (bool) $options['use_lua'];
        }

        if (isset($options['lua_max_c_stack'])) {
            $this->_luaMaxCStack = (int) $options['lua_max_c_stack'];
        }

        if (isset($options['retry_reads_on_master'])) {
            $this->_retryReadsOnMaster = (bool) $options['retry_reads_on_master'];
        }

        if (isset($options['auto_expire_lifetime'])) {
            $this->_autoExpireLifetime = (int) $options['auto_expire_lifetime'];
        }

        if (isset($options['auto_expire_pattern'])) {
            $this->_autoExpirePattern = (string) $options['auto_expire_pattern'];
        }

        if (isset($options['auto_expire_refresh_on_load'])) {
            $this->_autoExpireRefreshOnLoad = (bool) $options['auto_expire_refresh_on_load'];
        }
    }

    /**
     * Apply common configuration to client instances.
     *
     * @param \Relay\Relay $client
     * @param bool $forceSelect
     * @param object $clientOptions
     */
    protected function _applyClientOptions($client, $forceSelect = false, $clientOptions = null)
    {
        if ($clientOptions === null) {
            $clientOptions = $this->_clientOptions;
        }

        if ($clientOptions->password) {
            $client->auth($clientOptions->password)
                or Zend_Cache::throwException('Unable to authenticate with the redis server.');
        }

        if ($forceSelect || $clientOptions->database || $clientOptions->persistent) {
            $client->select($clientOptions->database)
                or Zend_Cache::throwException('The redis database could not be selected.');
        }
    }
}
