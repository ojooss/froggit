#Froggit Data Collector

Simple PHP-App to act as a collector for froggit data. 
Configure your froggit weather station to call this app via http(s).

Main file 'index.php' takes parameters (see below) and converts the 
values and store them into a given mysql database.

Finally, can trigger some webhooks. Beware of not to much, because you may run into timeouts.

##Sample POST-Parameter according to `ecowitt` Format 

@see https://www.ecowitt.net
    
    2021-01-09 08:27:08|Array
    (
        [PASSKEY] => ACA4D56B4A00A25083AF04701AC8FACF
        [stationtype] => EasyWeatherV1.4.6
        [dateutc] => 2021-01-09 08:27:07
        [tempinf] => 74.8
        [humidityin] => 36
        [baromrelin] => 29.292
        [baromabsin] => 29.292
        [tempf] => 32.0
        [humidity] => 95
        [winddir] => 96
        [winddir_avg10m] => 96
        [windspeedmph] => 0.0
        [windspdmph_avg10m] => 0.0
        [windgustmph] => 0.0
        [maxdailygust] => 0.0
        [rainratein] => 0.000
        [eventrainin] => 0.000
        [hourlyrainin] => 0.000
        [dailyrainin] => 0.000
        [weeklyrainin] => 0.012
        [monthlyrainin] => 0.012
        [yearlyrainin] => 0.012
        [solarradiation] => 50.65
        [uv] => 0
        [soilmoisture1] => 61
        [wh65batt] => 0
        [wh25batt] => 0
        [soilbatt1] => 1.6
        [freq] => 868M
        [model] => HP1000SE-PRO_Pro_V1.6.4
    )

See also https://heiligensee-wetter.de/code_1.php
