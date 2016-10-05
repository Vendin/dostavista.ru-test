<?php

use Cmfcmf\OpenWeatherMap;

function getWeatherInfo(string $location): string
{
    try {
        $owm = new OpenWeatherMap(ACCESS_TOKEN);
        $weather = $owm->getRawHourlyForecastData($location, UNITS, LANG, '', FORMAT);
        $weather = json_decode($weather, true);
        $result['city'] = array_splice($weather['city'],0, 3);
        $list = $weather['list'];
        $tempMin = array_reduce($list, function($carry, $item) {
            if ($item['main']['temp_min'] < $carry['main']['temp_min']) {
                $carry = $item;
            }
            return $carry;
        }, $list[0]);
        $dateTempMin = date('Y-m-j', $tempMin['dt']);
        $toDay = array_filter($list, function($val) use ($dateTempMin) {
            return date('Y-m-j', $val['dt']) == $dateTempMin;
        });
        $dayFromMaxWind = array_reduce($toDay, function($carry, $item) {
            if ($item['wind']['speed'] > $carry['wind']['speed']) {
                $carry = $item;
            }
            return $carry;
        }, $toDay[0]);
        $result['coldest_day']['date'] = $dateTempMin;
        $result['coldest_day']['min_temperature'] = $tempMin['main']['temp_min'];
        $result['coldest_day']['max_wind'] = $dayFromMaxWind['wind']['speed'];
        return json_encode($result);
    } catch (\Cmfcmf\OpenWeatherMap\Exception $e) {
        echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
    }catch (\Exception $e) {
        echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
    }
    return '';
}
