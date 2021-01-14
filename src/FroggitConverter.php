<?php /** @noinspection PhpUnusedPrivateMethodInspection */


class FroggitConverter
{

    /**
     * List of allowed parameter names
     * @var array
     */
    private array $whitelist = [
        'baromrelin' => 'barometer',
        'dailyrainin' => 'rain-daily',
        'eventrainin' => 'rain-event',
        'hourlyrainin' => 'rain-hourly',
        'humidity' => 'humidity-out',
        'humidityin' => 'humidity-in',
        'maxdailygust' => 'wind-gust-max-day',
        'rainratein' => 'rain-rate',
        'soilmoisture1' => 'soil-moisture-1',
        'tempf' => 'temperature-out',
        'tempinf' => 'temperature-in',
        'uv' => 'uv',
        'winddir_avg10m' => 'wind-direction-avg10m',
        'windspdmph_avg10m' => 'wind-speed-avg10m',
    ];

    private array $converterFunctions = [
        '^windsp.*' => 'convertWind',
        '.*gust.*' => 'convertWind',
        '^temp.*' => 'convertTemperature',
        '^barom.*' => 'convertBarometer',
        '.*rain.*' => 'convertRain',
    ];

    /**
     * @param array $values
     * @return array
     */
    public function dispatchRequest(array $values): array
    {
        $return = [];
        foreach ($values as $parameter => $value) {
            try {
                // check whitelist
                if (!array_key_exists($parameter, $this->whitelist)) {
                    continue;
                }

                // check for unit convert
                foreach($this->converterFunctions as $pattern => $convertFunction) {
                    if (preg_match('/'.$pattern.'/', $parameter)) {
                        $value = call_user_func_array( [$this, $convertFunction], [$value] );
                        break;
                    }
                }

                // lookup target name
                $parameter = $this->whitelist[$parameter];

                // finished
                $return[$parameter] = $value;

            } catch (Throwable $t) {
                error_log($t->getMessage());
            }
        }

        return $return;
    }

    /**
     * @param string $parameter
     * @param $value
     */
    private function dispatchParameter(string $parameter, $value): void
    {


    }

    /**
     * mph to ms
     *
     * @param string (float) $value
     * @return float
     */
    private function convertWind($value): float
    {
        return $value * 0.44704;
    }

    /**
     * f to c
     *
     * @param string (float) $value
     * @return float
     */
    private function convertTemperature($value):  float
    {
        return round(($value - 32) * (5/9), 1);
    }

    /**
     * inHG in hPA
     *
     * @param string (float) $value
     * @return int
     */
    private function convertBarometer($value): int
    {
        return round($value * 33.862);
    }

    /**
     * in to mm
     *
     * @param string (float) $value
     * @return int
     */
    private function convertRain($value): int
    {
        return round($value * 25.4);
    }

}
