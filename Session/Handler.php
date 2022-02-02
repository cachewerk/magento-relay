<?php

namespace CacheWerk\Relay\Session;

use SessionHandlerInterface;

use Magento\Framework\Filesystem;
use Magento\Framework\Session\SaveHandler\Redis as RedisSessionHandler;

use Cm\RedisSession\Handler\ConfigInterface;
use Cm\RedisSession\Handler\LoggerInterface;

class Handler implements SessionHandlerInterface
{
    /**
     *
     * @var mixed
     */
    protected $handler;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        ConfigInterface $config,
        LoggerInterface $logger,
        Filesystem $filesystem
    ) {
        if ($this->config->getClient() === 'relay') {
            $this->connection = new RelaySessionHandler($config, $logger);
        } else {
            $this->connection = new RedisSessionHandler($config, $logger, $filesystem);
        }
    }

    /**
     * Open session.
     *
     * @param string $savePath ignored
     * @param string $sessionName ignored
     * @return bool
     * @throws Magento\Framework\Exception\SessionException
     */
    #[\ReturnTypeWillChange]
    public function open($savePath, $sessionName)
    {
        return $this->connection->open($savePath, $sessionName);
    }

    /**
     * Fetch session data.
     *
     * @param string $sessionId
     * @return string|false
     * @throws Magento\Framework\Exception\SessionException
     */
    #[\ReturnTypeWillChange]
    public function read($sessionId)
    {
        return $this->connection->read($sessionId);
    }

    /**
     * Update session.
     *
     * @param string $sessionId
     * @param string $sessionData
     * @return boolean
     * @throws Magento\Framework\Exception\SessionException
     */
    #[\ReturnTypeWillChange]
    public function write($sessionId, $sessionData)
    {
        return $this->connection->write($sessionId, $sessionData);
    }

    /**
     * Destroy session.
     *
     * @param string $sessionId
     * @return boolean
     * @throws Magento\Framework\Exception\SessionException
     */
    #[\ReturnTypeWillChange]
    public function destroy($sessionId)
    {
        return $this->connection->destroy($sessionId);
    }

    /**
     * Overridden to prevent calling getLifeTime at shutdown.
     *
     * @return bool
     * @throws Magento\Framework\Exception\SessionException
     */
    #[\ReturnTypeWillChange]
    public function close()
    {
        return $this->connection->close();
    }

    /**
     * Garbage collection.
     *
     * @param int $maxLifeTime ignored
     * @return boolean
     * @throws Magento\Framework\Exception\SessionException
     */
    #[\ReturnTypeWillChange]
    public function gc($maxLifeTime)
    {
        return $this->connection->gc($maxLifeTime);
    }

    /**
     * Get the number of failed lock attempts.
     *
     * @return int
     * @throws Magento\Framework\Exception\SessionException
     */
    public function getFailedLockAttempts()
    {
        return $this->connection->getFailedLockAttempts();
    }
}
