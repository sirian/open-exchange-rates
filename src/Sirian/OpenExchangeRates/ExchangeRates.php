<?php

namespace Sirian\OpenExchangeRates;

class ExchangeRates
{
    protected $base;

    protected $rates;

    public function __construct($rates = array(), $base = 'USD')
    {
        $this->setRates($rates);
        $this->base = 'USD';
    }

    public function setRate($currency, $rate)
    {
        $this->rates[mb_strtoupper($currency)] = $rate;
        return $this;
    }

    public function setRates(array $rates)
    {
        $this->rates = [];
        foreach ($rates as $currency => $rate) {
            $this->setRate($currency, $rate);
        }
        return $this;
    }

    public function getBase()
    {
        return $this->base;
    }

    public function setBase($base)
    {
        $this->base = $base;
        return $this;
    }

    public function convert($value, $from, $to)
    {
        if ($from == $to) {
            return $value;
        }

        if (!isset($this->rates[$from], $this->rates[$to])) {
            throw new \InvalidArgumentException();
        }

        return $value * $this->rates[$to] / $this->rates[$from];
    }
}
