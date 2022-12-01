<?php

namespace CacheWerk\Relay\Session;

use Cm\RedisSession\Handler\ConfigInterface;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface as StoreScopeInterface;
use Magento\Framework\Session\SaveHandler\Redis\Config as RedisConfig;

class Config extends RedisConfig implements ConfigInterface
{
    /**
     * @inheritdoc
     */
    private $deploymentConfig;

    /**
     * @inheritdoc
     */
    private $scopeConfig;

    /**
     * @inheritdoc
     */
    private $appState;

    /**
     * Configuration path for client.
     */
    const PARAM_CLIENT = 'session/redis/client';

    /**
     * {@inheritdoc}
     */
    public function __construct(
        DeploymentConfig $deploymentConfig,
        State $appState,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->deploymentConfig = $deploymentConfig;
        $this->appState = $appState;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get client name.
     *
     * @return string
     */
    public function getClient()
    {
        return $this->deploymentConfig->get(self::PARAM_CLIENT);
    }

    /**
     * @inheritdoc
     */
    public function getLogLevel()
    {
        return $this->deploymentConfig->get(self::PARAM_LOG_LEVEL);
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->deploymentConfig->get(self::PARAM_HOST);
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->deploymentConfig->get(self::PARAM_PORT);
    }

    /**
     * @inheritdoc
     */
    public function getDatabase()
    {
        return (int) $this->deploymentConfig->get(self::PARAM_DATABASE);
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->deploymentConfig->get(self::PARAM_PASSWORD);
    }

    /**
     * @inheritdoc
     */
    public function getTimeout()
    {
        return $this->deploymentConfig->get(self::PARAM_TIMEOUT);
    }

    /**
     * @inheritdoc
     */
    public function getPersistentIdentifier()
    {
        return $this->deploymentConfig->get(self::PARAM_PERSISTENT_IDENTIFIER);
    }

    /**
     * @inheritdoc
     */
    public function getCompressionThreshold()
    {
        return $this->deploymentConfig->get(self::PARAM_COMPRESSION_THRESHOLD);
    }

    /**
     * @inheritdoc
     */
    public function getCompressionLibrary()
    {
        return $this->deploymentConfig->get(self::PARAM_COMPRESSION_LIBRARY);
    }

    /**
     * @inheritdoc
     */
    public function getMaxConcurrency()
    {
        return $this->deploymentConfig->get(self::PARAM_MAX_CONCURRENCY);
    }

    /**
     * @inheritdoc
     */
    public function getMaxLifetime()
    {
        return self::SESSION_MAX_LIFETIME;
    }

    /**
     * @inheritdoc
     */
    public function getMinLifetime()
    {
        return $this->deploymentConfig->get(self::PARAM_MIN_LIFETIME);
    }

    /**
     * @inheritdoc
     */
    public function getDisableLocking()
    {
        return $this->deploymentConfig->get(self::PARAM_DISABLE_LOCKING);
    }

    /**
     * @inheritdoc
     */
    public function getBotLifetime()
    {
        return $this->deploymentConfig->get(self::PARAM_BOT_LIFETIME);
    }

    /**
     * @inheritdoc
     */
    public function getBotFirstLifetime()
    {
        return $this->deploymentConfig->get(self::PARAM_BOT_FIRST_LIFETIME);
    }

    /**
     * @inheritdoc
     */
    public function getFirstLifetime()
    {
        return $this->deploymentConfig->get(self::PARAM_FIRST_LIFETIME);
    }

    /**
     * @inheritdoc
     */
    public function getBreakAfter()
    {
        return $this->deploymentConfig->get(self::PARAM_BREAK_AFTER . '_' . $this->appState->getAreaCode());
    }

    /**
     * @inheritdoc
     */
    public function getLifetime()
    {
        if ($this->appState->getAreaCode() == Area::AREA_ADMINHTML) {
            return (int)$this->scopeConfig->getValue(self::XML_PATH_ADMIN_SESSION_LIFETIME);
        }

        return (int)$this->scopeConfig->getValue(self::XML_PATH_COOKIE_LIFETIME, StoreScopeInterface::SCOPE_STORE);
    }

    /**
     * @inheritdoc
     */
    public function getSentinelServers()
    {
        return $this->deploymentConfig->get(self::PARAM_SENTINEL_SERVERS);
    }

    /**
     * @inheritdoc
     */
    public function getSentinelMaster()
    {
        return $this->deploymentConfig->get(self::PARAM_SENTINEL_MASTER);
    }

    /**
     * @inheritdoc
     */
    public function getSentinelVerifyMaster()
    {
        return $this->deploymentConfig->get(self::PARAM_SENTINEL_VERIFY_MASTER);
    }

    /**
     * @inheritdoc
     */
    public function getSentinelConnectRetries()
    {
        return $this->deploymentConfig->get(self::PARAM_SENTINEL_CONNECT_RETRIES);
    }

    /**
     * @inheritdoc
     */
    public function getFailAfter()
    {
        return self::DEFAULT_FAIL_AFTER;
    }
}
