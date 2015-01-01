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
        $key = 'historical:' . $this->getUTCDate($date);
        if ($this->cache->contains($key)) {
            return unserialize($this->cache->fetch($key));
        } else {
            if ($this->getUTCDate(new \DateTime()) == $this->getUTCDate($date)) {
                $rates = parent::getLatest();
            } else {
                $rates = parent::getHistorical($date);
            }

            $this->cache->save($key, serialize($rates));
            return $rates;
        }
    }
}
