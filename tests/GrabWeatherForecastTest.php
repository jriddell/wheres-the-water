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
        $this->assertEquals('light rain', $this->forecast->forecastHtml());
    }
}
