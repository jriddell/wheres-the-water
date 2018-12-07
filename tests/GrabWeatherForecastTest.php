<?php

require_once('../config.php');
require_once('../lib/GrabWeatherForecast.php');

use PHPUnit\Framework\TestCase;

final class GrabWeatherForecastTest extends TestCase
{
    public function initGrabWeatherForecastTest() {
        $this->forecast = new GrabWeatherForecast();
        $this->forecast->doGrabWeatherForecast("234189", "57.0759", "-4.84497"); // Garry
        $weatherData = file_get_contents('data/forecast.json');
        $this->forecast->weatherForecast = json_decode($weatherData, true);
    }

    /*
    public function testValidateWeatherData() {
        $this->initGrabWeatherForecastTest();
        $this->assertEquals(true, $this->forecast->validateWeatherData());
    }
    */

    public function testForecastHtml() {
        $this->initGrabWeatherForecastTest();
        $this->assertEquals("<div style='font-size: smaller'>Fri 9:00:light rain <img src='http://openweathermap.org/img/w/10d.png' width='50' height='50'/><br /></div><div style='font-size: smaller'>Fri 15:00:light rain <img src='http://openweathermap.org/img/w/10d.png' width='50' height='50'/><br /></div><div style='font-size: smaller'>Sat 9:00:overcast clouds <img src='http://openweathermap.org/img/w/04d.png' width='50' height='50'/><br /></div><div style='font-size: smaller'>Sat 15:00:light rain <img src='http://openweathermap.org/img/w/10d.png' width='50' height='50'/><br /></div><div style='font-size: smaller'>Sun 9:00:clear sky <img src='http://openweathermap.org/img/w/01d.png' width='50' height='50'/><br /></div><div style='font-size: smaller'>Sun 15:00:light rain <img src='http://openweathermap.org/img/w/10d.png' width='50' height='50'/><br /></div>", $this->forecast->forecastHtml());
    }
}
