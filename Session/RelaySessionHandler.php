<?php

namespace CacheWerk\Relay\Session;

use Exception;
use LogicException;

use Relay\Relay;

use Cm\RedisSession\Handler;
use Cm\RedisSession\ConnectionFailedException;
use Cm\RedisSession\Handler\ConfigInterface;
use Cm\RedisSession\Handler\LoggerInterface;

class RelaySessionHandler extends Handler
{
    /**
     * @var Relay
     */
    protected $_redis;

    /**
     * {@inheritdoc}
     */
    public function __construct(ConfigInterface $config, LoggerInterface $logger, $readOnly = false)
    {
        $this->config = $config;
        $this->logger = $logger;

        $this->logger->setLogLevel($this->config->getLogLevel() ?: self::DEFAULT_LOG_LEVEL);

        $timeStart = microtime(true);

        $this->_dbNum = $this->config->getDatabase() ?: self::DEFAULT_DATABASE;
        $this->_readOnly = $readOnly;
        $this->_compressionThreshold = $this->config->getCompressionThreshold() ?: self::DEFAULT_COMPRESSION_THRESHOLD;
        $this->_compressionLibrary = $this->config->getCompressionLibrary() ?: self::DEFAULT_COMPRESSION_LIBRARY;
        $this->_maxConcurrency = $this->config->getMaxConcurrency() ?: self::DEFAULT_MAX_CONCURRENCY;
        $this->_failAfter = $this->config->getFailAfter() ?: self::DEFAULT_FAIL_AFTER;
        $this->_maxLifetime = $this->config->getMaxLifetime() ?: self::DEFAULT_MAX_LIFETIME;
        $this->_minLifetime = $this->config->getMinLifetime() ?: self::DEFAULT_MIN_LIFETIME;
        $this->_useLocking = ! $this->config->getDisableLocking();

        // Use sleep time multiplier so fail after time is in seconds
        $this->_failAfter = (int) round((1000000 / self::SLEEP_TIME) * $this->_failAfter);

        $sentinelServers = $this->config->getSentinelServers();
        $sentinelMaster = $this->config->getSentinelMaster();

        if ($sentinelServers && $sentinelMaster) {
            throw new LogicException('Relay does not support Redis Sentinel.');
        }

        $this->_redis = new Relay;

        if ($this->hasConnection() == false) {
            throw new ConnectionFailedException('Unable to connect to Redis');
        }

        $this->_log(
            sprintf(
                "%s initialized for connection to %s:%s after %.5f seconds",
                get_class($this),
                $this->_redis->getHost(),
                $this->_redis->getPort(),
                microtime(true) - $timeStart
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function hasConnection()
    {
        $host = $this->config->getHost() ?: self::DEFAULT_HOST;
        $port = $this->config->getPort() ?: self::DEFAULT_PORT;
        $pass = $this->config->getPassword() ?: null;
        $timeout = $this->config->getTimeout() ?: self::DEFAULT_TIMEOUT;

        try {
            $this->_redis->connect($host, $port, $timeout);

            if ($pass) {
                $this->_redis->auth($pass);
            }

            $this->_log('Connected to Redis');

            return true;
        } catch (Exception $e) {
            $this->logger->logException($e);
            $this->_log('Unable to connect to Redis');

            return false;
        }
    }
}
