<?php /** @noinspection PhpUnusedPrivateMethodInspection */


class FroggitConverter
{

    /**
     * List of allowed parameter names
     * @var array (attribute => name)
     */
    private array $whitelist = [];

    /**
     * Array of skipped value (not in whitelist)
     * @var array
     */
    private array $skippedValues = [];

    /**
     * Convert or Recalculate values matching pattern
     * @var array (pattern => functionName)
     */
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
                    $this->skippedValues[$parameter] = $value;
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
                $this->skippedValues[$parameter] = 'ERROR: ' . $t->getMessage();
            }
        }

        return $return;
    }

    /**
     * @param $attribute
     * @param $name
     */
    public function addParameter($attribute, $name)
    {
        $this->whitelist[$attribute] = $name;
    }

    /**
     * @return array
     */
    public function getSkippedValues(): array
    {
        return $this->skippedValues;
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
