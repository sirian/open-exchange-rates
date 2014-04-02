<?php

namespace Sirian\OpenExchangeRates;


use Doctrine\Common\Cache\Cache;

class CachedApiClient extends ApiClient
{
    protected $cache;

    public function __construct(Cache $cache, $appId, $secure = false)
    {
        $this->cache = $cache;

        parent::__construct($appId, $secure);
    }

    public function getHistorical(\DateTime $date)
    {
        $key = 'historical:' . $date->format('Y-m-d');
        if ($this->cache->contains($key)) {
            return unserialize($this->cache->fetch($key));
        } else {
            $rates = parent::getHistorical($date);
            $this->cache->save($key, serialize($rates));
            return $rates;
        }
    }
}
