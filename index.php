<?php

include_once __DIR__ . '/src/Configuration.php';
include_once __DIR__ . '/src/FroggitConverter.php';


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


try {
    $configuration = new Configuration();
    $converter = new FroggitConverter();

    $dbh = mysqli_connect(
        $configuration->getDatabaseHost(),
        $configuration->getDatabaseUser(),
        $configuration->getDatabasePassword(),
        $configuration->getDatabaseName(),
        $configuration->getDatabasePort()
    );

    $data = $converter->dispatchRequest($_POST);

    foreach ($data as $attribute => $value) {

        $sql = "INSERT INTO ".$configuration->getDatabaseTable().
            "   (timestamp, device, attribute, value) ".
            "VALUES ".
            "   (NOW(), 'froggit', '".$attribute."', ".$value.") ".
            "ON DUPLICATE KEY UPDATE " .
            "   value = " . $value;
        #echo $sql . PHP_EOL;
        if (false === mysqli_query($dbh, $sql)) {
            throw new \RuntimeException(mysqli_error($dbh) . PHP_EOL . $sql);
        }
    }

    if ($dbh) {
        mysqli_close($dbh);
    }

    #file_put_contents('php://stdout', date("Y-m-d H:i:s").'|'.print_r($converter, true));
    echo 'SUCCESS';
} catch (Exception $e) {
    file_put_contents('php://stdout', date("Y-m-d H:i:s").'| ERROR: ' . $e->getMessage() . PHP_EOL);
    echo 'FAILURE';
}
