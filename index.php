<?php

include_once __DIR__ . '/src/Configuration.php';
include_once __DIR__ . '/src/FroggitConverter.php';

/*
$_POST = array(
    'PASSKEY' => 'ACA4D56B4A00A25083AF04701AC8FACF',
    'stationtype' => 'EasyWeatherV1.4.6',
    'dateutc' => '2021-01-09 20:48:47',
    'tempinf' => '74.1',
    'humidityin' => '34',
    'baromrelin' => '30.319',
    'baromabsin' => '29.365',
    'tempf' => '23.7',
    'humidity' => '97',
    'winddir' => '44',
    'winddir_avg10m' => '44',
    'windspeedmph' => '0.0',
    'windspdmph_avg10m' => '0.0',
    'windgustmph' => '0.0',
    'maxdailygust' => '6.9',
    'rainratein' => '0.000',
    'eventrainin' => '0.000',
    'hourlyrainin' => '0.000',
    'dailyrainin' => '0.000',
    'weeklyrainin' => '0.012',
    'monthlyrainin' => '0.012',
    'yearlyrainin' => '0.012',
    'solarradiation' => '0.00',
    'uv' => '0',
    'soilmoisture1' => '60',
    'wh65batt' => '0',
    'wh25batt' => '0',
    'soilbatt1' => '1.6',
    'freq' => '868M',
    'model' => 'HP1000SE-PRO_Pro_V1.6.4',
);
*/

try {
    /*
     * init objects
     */
    $configuration = new Configuration();
    $converter = new FroggitConverter();

    /*
     * init parameters
     */
    $converter->addParameter('baromrelin', 'barometer');
    $converter->addParameter('dailyrainin', 'rain-daily');
    $converter->addParameter('eventrainin', 'rain-event');
    $converter->addParameter('hourlyrainin', 'rain-hourly');
    $converter->addParameter('humidity', 'humidity-out');
    $converter->addParameter('humidityin', 'humidity-in');
    $converter->addParameter('maxdailygust', 'wind-gust-max-day');
    $converter->addParameter('rainratein', 'rain-rate');
    $converter->addParameter('soilmoisture1', 'soil-moisture-1');
    $converter->addParameter('tempf', 'temperature-out');
    $converter->addParameter('tempinf', 'temperature-in');
    $converter->addParameter('uv', 'uv');
    $converter->addParameter('winddir_avg10m', 'wind-direction-avg10m');
    $converter->addParameter('windspdmph_avg10m', 'wind-speed-avg10m');

    /*
     * try to connect DB
     */
    $dbh = mysqli_connect(
        $configuration->getDatabaseHost(),
        $configuration->getDatabaseUser(),
        $configuration->getDatabasePassword(),
        $configuration->getDatabaseName(),
        $configuration->getDatabasePort()
    );
    if (!$dbh) {
        throw new RuntimeException( mysqli_connect_error() );
    }

    /*
     * handle requests
     */
    if (!empty($_POST)) {
        $data = $converter->dispatchRequest($_POST);
    } elseif (!empty($_GET)) {
        $data = $converter->dispatchRequest($_GET);
    } else {
        $data = [];
    }

    /*
     *  save to database
     */
    foreach ($data as $attribute => $value) {
        $sql = "INSERT INTO ".$configuration->getDatabaseTable().
            "   (timestamp, device, attribute, value) ".
            "VALUES ".
            "   (NOW(), 'froggit', '".$attribute."', ".$value.") ".
            "ON DUPLICATE KEY UPDATE " .
            "   value = " . $value;
        #echo $sql . PHP_EOL;
        if (false === mysqli_query($dbh, $sql)) {
            throw new RuntimeException(mysqli_error($dbh) . PHP_EOL . $sql);
        }
    }

    /*
     * close connection
     */
    if ($dbh) {
        mysqli_close($dbh);
    }

    /*
     * log for debug purpose
     */
    file_put_contents(__DIR__ . '/debug.log', print_r($data, true));
    file_put_contents(__DIR__ . '/skipped.log', print_r($converter->getSkippedValues(), true));
    file_put_contents(__DIR__ . '/error.log', '');

    /*
     * return 200 OK
     */
    echo 'SUCCESS';

} catch (Exception $e) {

    /*
     * log error
     */
    file_put_contents(__DIR__ . '/error.log', print_r($e, true));

    /*
     * return failure
     */
    echo 'FAILURE';
}
