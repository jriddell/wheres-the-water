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
        $weatherFileURL = $this->forecastAPIURL . "?lat=" . $latitude . "&lon=" . $longitude . "&units=metric&appid=" . OPENWEATHERKEY;
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
        print " XXX forecastHTML()\n";
        $html = "";
        $max_forecasts = 6;
        $count = 0;
        foreach($this->weatherForecast['list'] as $forecast) {
            print "XXX foreach list\n";
            //show weather at 9 o'clock in morning and 3 in afternoon
            print "XXX dt: " . date('G', $forecast['dt']) . "\n";
            if ($count < $max_forecasts and (date('G', $forecast['dt']) == "9" or date('G', $forecast['dt']) == "15")) {
                print "XXX inside if\n";
                $count = $count + 1;
                $windSpeed = round($forecast['wind']['speed'] * 3.6, 1); // convert to km/h
                $temperature = round($forecast['main']['temp'], 1);
                $html .= "<span class='riverForecast'>";
                $html .= date('D ', $forecast['dt']);
                //$html .= "<br />";
                $html .= date('G:i', $forecast['dt']);
                $html .= "<br />";
                $html .= $temperature . "°C ";
                $html .= $windSpeed . "km/h";
                $html .= "<br />";
                $html .= "<img src='https://openweathermap.org/img/w/".$forecast['weather'][0]['icon'].".png' width='35' height='35'/>";
                $html .= "&nbsp;</span>";
                if ($count % 2 == 0) {
                    $html .= "<br />";
                }
            }
            print "XXX foreach list done \n";
        }
        print " XXX forecast: $html";
        return $html;
    }
}
