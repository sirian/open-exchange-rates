<?php

namespace Sirian\OpenExchangeRates;

class ApiClient
{
    protected $appId;

    public function __construct($appId, $secure = false)
    {
        $this->appId = $appId;
        $this->secure = $secure;
    }

    public function getLatest()
    {
        return $this->createRates($this->request('latest.json'));
    }

    public function getHistorical(\DateTime $date)
    {
        return $this->createRates($this->request('historical/' . $this->getUTCDate($date) . '.json'));
    }

    public function getEndpoint()
    {
        return ($this->secure ? 'https' : 'http') . '://openexchangerates.org';
    }

    protected function request($method, $params = array())
    {
        $url = $this->getEndpoint() . '/api/' . $method . '?' . http_build_query(array_merge(array(
            'app_id' => $this->appId
        ), $params));

        $rawData = file_get_contents($url);
        if (false === $rawData) {
            throw new Exception('Could not request ' . $url);
        }

        $data = json_decode($rawData, true);

        if (JSON_ERROR_NONE != json_last_error()) {
            throw new Exception('Invalid json response: ' . json_last_error_msg());
        }

        return $data;
    }

    protected function getUTCDate(\DateTime $date)
    {
        $date = clone $date;
        $date->setTimezone(new \DateTimeZone('UTC'));
        return $date->format('Y-m-d');
    }

    private function createRates($data)
    {
        $rates = new ExchangeRates($data['rates'], $data['base']);
        return $rates;
    }
}
