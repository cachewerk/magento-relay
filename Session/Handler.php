<?php

namespace CacheWerk\Relay\Session;

use SessionHandlerInterface;

use Magento\Framework\Filesystem;
use Magento\Framework\Session\SaveHandler\Redis;

use Cm\RedisSession\Handler\ConfigInterface;
use Cm\RedisSession\Handler\LoggerInterface;

class Handler extends Redis implements SessionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        ConfigInterface $config,
        LoggerInterface $logger,
        Filesystem $filesystem
    ) {
        if ($config->getClient() === 'relay') {
            // ...
        }

        parent::__construct($config, $logger, $filesystem);
    }
}
