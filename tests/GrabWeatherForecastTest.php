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
        $this->assertEquals('Fri 7 Dec 2018, 9:00 light rain http://openweathermap.org/img/w/10d.png<br />Fri 7 Dec 2018, 15:00 light rain http://openweathermap.org/img/w/10d.png<br />Sat 8 Dec 2018, 9:00 overcast clouds http://openweathermap.org/img/w/04d.png<br />Sat 8 Dec 2018, 15:00 light rain http://openweathermap.org/img/w/10d.png<br />Sun 9 Dec 2018, 9:00 clear sky http://openweathermap.org/img/w/01d.png<br />Sun 9 Dec 2018, 15:00 light rain http://openweathermap.org/img/w/10d.png<br />Mon 10 Dec 2018, 9:00 light rain http://openweathermap.org/img/w/10d.png<br />Mon 10 Dec 2018, 15:00 light rain http://openweathermap.org/img/w/10d.png<br />Tue 11 Dec 2018, 9:00 few clouds http://openweathermap.org/img/w/02d.png<br />Tue 11 Dec 2018, 15:00 light rain http://openweathermap.org/img/w/10d.png<br />', $this->forecast->forecastHtml());
    }
}
