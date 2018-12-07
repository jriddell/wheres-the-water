<?php
/* Copyright 2018 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only
*/

/* Downloads and makes available the weather forecast from openweathermap.org
    in config.php set your API key:
    define("OPENWEATHERKEY", "1234abcd1234abcd");
    call doGrabWeatherForecast() and it will set $this->weatherForecast with the forecast data
    See format docs at:
    https://openweathermap.org/forecast5
    https://openweathermap.org/weather-conditions
*/

class GrabWeatherForecast {
    const DATADIR = 'data';
    const OPENWEATHER_DOWNLOAD_PERIOD = 3600; // 60 * 60; // make sure current download is no older than 1 hour
    const OPENWEATHER_URL = 'https://api.openweathermap.org/data/2.5/forecast';

    function __construct() {
        $this->forecastAPIURL = self::OPENWEATHER_URL;
        $this->dataDir = ROOT . '/' . self::DATADIR;
    }
    
    public function doGrabWeatherForecast($gauge_id, $longitude, $latitude) {
        $weatherFilename = "${gauge_id}-weather.json";
        $weatherFilePath = $this->dataDir . '/' . $weatherFilename;
        $weatherFileURL = $this->forecastAPIURL . "?lat=" . $latitude . "&lon=" . $longitude . "&appid=" . OPENWEATHERKEY;
        if (!file_exists($weatherFilePath) || time()-filemtime($weatherFilePath) > self::OPENWEATHER_DOWNLOAD_PERIOD) {
            $weatherData = @file_get_contents($weatherFileURL);
            if($weatherData == false) {
                print "<p>No OpenWeatherMap gauge data for " . $gauge_id . "</p>\n";
                flush();
                return False;
            }
            if (!$this->validateWeatherData($weatherData)) {
                print "<p>Empty OpenWeatherMap file downloaded for " . $gauge_id . "</p>\n";
                $this->currentReading = -1;
                flush();
                return False;
            }
            $newWeatherFile = fopen($weatherFilePath, "w") or die("Unable to open file!");
            fwrite($newWeatherFile, $weatherData);
        } else {
            $weatherData = file_get_contents($weatherFilePath);
        }
        $this->weatherForecast = json_decode($weatherData, true);
    }

    private function validateWeatherData($weatherData) {
        //TODO
        return true;
    }
    
    public function forecastHtml() {
        $html = "";
        $max_forecasts = 6;
        $count = 0;
        foreach($this->weatherForecast['list'] as $forecast) {
            //show weather at 9 o'clock in morning and 3 in afternoon
            if ($count < $max_forecasts and (date('G', $forecast['dt']) == "9" or date('G', $forecast['dt']) == "15")) {
                $count = $count + 1;
                $html .= "<span class='riverForecast'>";
                $html .= "<img src='http://openweathermap.org/img/w/".$forecast['weather'][0]['icon'].".png' width='35' height='35'/>";
                $html .= "<br />";
                $html .= date('D', $forecast['dt']);
                $html .= "<br />";
                $html .= date('G:i', $forecast['dt']);
                $html .= "</span>&nbsp;";
                if ($count % 2 == 0) {
                    $html .= "<br />";
                }
            }
        }
        return $html;
    }
}
