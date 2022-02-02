<?php

namespace CacheWerk\Relay\Cache\Backend;

use Cm_Cache_Backend_Redis;

class Relay extends Cm_Cache_Backend_Redis
{
    /**
     * {@inheritdoc}
     */
     public function __construct($options = [])
     {
         parent::__construct($options);
     }
}
