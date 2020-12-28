<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Currency extends Model
{
    const RATES_TBL = 'plugin_currency_rates';
    const ARCHIVE_TBL = 'plugin_currency_rates_archive';
    const CURRENCIES_TBL = 'plugin_currency_currencies';
    public static $rates = null;

    public static function addCurrency($currency, $name, $symbol, $published = 1)
    {
        $result = DB::insert(self::CURRENCIES_TBL)
            ->values(
                array(
                    'currency' => $currency,
                    'name' => $name,
                    'symbol' => $symbol,
                    'published' => $published
                )
            )
            ->execute();
        return $result[0];
    }

    public static function removeCurrency($currency)
    {
        return DB::update(self::CURRENCIES_TBL)
            ->set(array('deleted' => 1, 'published' => 0))
            ->where('currency', '=', $currency)
            ->execute();
    }

    public static function getCurrencies($cached = false)
    {
        static $cache = null;
        if ($cached && $cache != null) {
            $currencies = $cache;
        } else {
            $rows = DB::select('currencies.*')
                ->from(array(self::CURRENCIES_TBL, 'currencies'))
                ->and_where('currencies.deleted', '=', 0)
                ->execute()
                ->as_array();
            $currencies = array();
            foreach ($rows as $row) {
                $currencies[$row['currency']] = $row;
            }
            $cache = $currencies;
        }
        return $currencies;
    }

    public static function getRates()
    {
        $rows = DB::select('*')
            ->from(self::RATES_TBL)
            ->execute()
            ->as_array();
        $rates = array();
        foreach ($rows as $row) {
            $rates[$row['currency']] = $row['rate'];
        }
        return $rates;
    }

    public static function getRatesFromXE($baseCurrency)
    {
        $currencies = self::getCurrencies();
        $rates = array();
        foreach ($currencies as $currency) {
            if ($currency['currency'] != $baseCurrency) {
                $html = file_get_contents('http://www.xe.com/?r=10&c=' . $currency['currency']);
                if (preg_match(
                    "#<a href='/currencycharts/\?from=" . $baseCurrency . "&amp;to=(" . $currency['currency'] . ")'.*?>(.*?)</a>#i",
                    $html,
                    $matches
                )) {
                    $rates[$matches[1]] = $matches[2];
                }
            }
        }
        return $rates;
    }

    public static function updateRatesFromXE()
    {
        try {
            Database::instance()->begin();
            $baseCurrency = Settings::instance()->get('currency_base');
            $rates = self::getRatesFromXE($baseCurrency);
            self::setRate($baseCurrency, 1, $baseCurrency);
            foreach ($rates as $currency => $rate) {
                self::setRate($currency, $rate, $baseCurrency);
            }
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function setRate($currency, $rate, $baseCurrency, $updated = null)
    {
        if ($updated == null) {
            $updated = date('Y-m-d H:i:s');
        }

        DB::delete(self::RATES_TBL)->where('currency', '=', $currency)->execute();
        DB::insert(self::RATES_TBL)->values(array('currency' => $currency, 'rate' => $rate, 'updated' => $updated))->execute();
        if ($currency != $baseCurrency) {
            DB::insert(self::ARCHIVE_TBL)
                ->values(array(
                    'currency' => $currency,
                    'base' => $baseCurrency,
                    'rate' => $rate,
                    'updated' => $updated
                ))->execute();
        }
    }

    public static function convert($amount, $toCurrency = null, $fromCurrency = null, $round = 2)
    {
        if (self::$rates == null) {
            self::$rates = self::getRates();
        }

        if ($toCurrency == null) {
            $toCurrency = self::getPreferredCurrency();
        }

        if ($fromCurrency == null) {
            $fromCurrency = Settings::instance()->get('currency_base');
        }

        $toCurrency = strtoupper($toCurrency);
        $fromCurrency = strtoupper($fromCurrency);

        $toRate = self::$rates[$toCurrency];
        $fromRate = self::$rates[$fromCurrency];

        $converted = round($amount / ($fromRate / $toRate), $round);
        return $converted;
    }

    public static function getCurrencySelector($selected = null)
    {
        if ($selected == null) {
            $selected = self::getPreferredCurrency();
        }
        $html = '<select class="currency-selector" onchange="setPreferredCurrency(this.value);">';
        $currencies = self::getCurrencies();
        foreach ($currencies as $currency) {
            if ($currency['published']) {
                $html .= '<option values="' . $currency['currency'] . '"' . ($selected == $currency['currency'] ? ' selected="selected"' : '') . '>' . $currency['currency'] . '</option>';
            }
        }
        $html .= '</select>';
        return $html;
    }

    public static function getPreferredCurrency($cached = false)
    {
        static $cache = null;
        if ($cached && $cache != null) {
            $currency = $cache;
        } else {
            $currency = Cookie::get('currency-preferred');
            if ($currency == null) {
                $currency = Settings::instance()->get('currency_base');
            }
            $cache = $currency;
        }
        return $currency;
    }

    public static function setPreferredCurrency($currency)
    {
        Cookie::set('currency-preferred', $currency);
    }
}