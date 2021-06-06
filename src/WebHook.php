<?php


class WebHook
{

    /**
     *
     * Name of Webhook
     * @var string
     */
    protected string $name;

    /**
     * Name of Froggit-Parameter, that triggers this webhook
     *
     * @var string
     */
    protected string $parameterName;

    /**
     * URL to be called (GET only)
     *
     * @var string
     */
    protected string $url;

    /**
     * @var float|null
     */
    protected ?float $lowerThreshold = null;

    /**
     * @var float|null
     */
    protected ?float $upperThreshold = null;

    /**
     *
     * @var string (error, info, debug)
     */
    protected string $logLevel = 'error';

    /**
     * @param string $pathToConfigFile
     * @return WebHook[]
     * @throws Exception
     */
    public static function factory(string $pathToConfigFile): array
    {
        $webhookConfig = json_decode(file_get_contents($pathToConfigFile), true);
        if ($webhookConfig) {
            $return = [];
            foreach ($webhookConfig as $name => $config) {
                $webhook = new self();
                $webhook->name = $name;
                $webhook->parameterName = $config['on-parameter-name'];
                $webhook->url = $config['url'];
                $webhook->lowerThreshold = ($config['lower-threshold'] ?? null);
                $webhook->upperThreshold = ($config['upper-threshold'] ?? null);
                $webhook->logLevel = ($config['log-level'] ?? 'error');
                $return[] = $webhook;
            }
            return $return;
        } else {
            throw new Exception('webhooks.json empty or invalid');
        }
    }

    /**
     * @throws Exception
     */
    public function trigger(array $data): array
    {
        $return = [];
        if ($this->logLevel === 'info' || $this->logLevel === 'debug') {
            $return[] = '### '.$this->name . ' ###';
            $return[] = '[i] time: '.date("Y-m-d H:i:s");
            $return[] = '[i] url: '.$this->url;
        }

        if (!isset($data[$this->parameterName])) {
            throw new Exception('No data found for webhook ' . $this->name . ' ('.$this->parameterName.')');
        }

        $value = $data[$this->parameterName];
        if ($this->logLevel === 'debug') {
            $return[] = '[d] value: '.$value;
        }

        if (null !== $this->lowerThreshold && $value < $this->lowerThreshold) {
            $return[] = '[d] skipped due to lower threshold ('.$value.' < '.$this->lowerThreshold.')';
            return $return;
        }

        if (null !== $this->upperThreshold && $value > $this->upperThreshold) {
            $return[] = '[d] skipped due to upper threshold ('.$value.' > '.$this->upperThreshold.')';
            return $return;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, ($this->logLevel === 'debug'));

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($response === false) {
            throw new Exception("CURL-Error in '".$this->name."' for '".$this->url."' : " . $error);
        }

        if ($this->logLevel === 'debug') {
            $return[] = '[d] parameter-name: '.$this->parameterName;
            $return[] = '[d] lower-threshold: '.$this->lowerThreshold;
            $return[] = '[d] upper-threshold: '.$this->upperThreshold;
            $return = array_merge($return, explode(PHP_EOL, $response));
        }

        return $return;
    }

}
