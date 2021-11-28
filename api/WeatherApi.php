<?php

namespace App\Api;

/**
 * Recieves requests at endpoint /api/weather/
 * Uses weather.gov API. See https://www.weather.gov/documentation/services-web-api
 */
class WeatherApi extends BaseApi
{
    private $service_url = 'https://api.weather.gov';

    /**
     * Sends GET request to service url with the path provided. 
     */
    private function send_api_request(string $path)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "$this->service_url/$path");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:19.0) Gecko/20100101 Firefox/19.0");
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept: application/geo+json']);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new \Exception("Error requesting weather data: " . curl_error($curl));
        }
        curl_close($curl);
        return $response;
    }

    /**
     * Retrieves the forecast 
     * @param string $office_id The 3 digit id of the office 
     */
    private function get_forecast(string $office_id, int $grid_x, int $grid_y)
    {
        $url_path = "gridpoints/$office_id/$grid_x,$grid_y/forecast";
        return $this->send_api_request($url_path);
    }

    /**
     * Sends GET request to https://api.weather.gov/gridpoints/OKX/31,34/forecast. 
     * Responds with Forecast period data from office with ID QKX and gridpoint 
     * Endpoint: /api/weather/office/forecast
     * 
     * @return string JSON array containing forecast periods. 
     */
    public function action_forecast(): string
    {
        $response = $this->get_forecast('OKX', 31, 34);
        $forecast = json_decode($response, true);
        $periods  = $forecast['properties']['periods'];

        $keys_to_include = [
            'name',
            'startTime',
            'endTime',
            'temperature',
            'temperatureUnit',
            'temperatureTrend',
            'icon',
            'shortForecast'
        ];

        $mapped_periods = array_map(function ($period) use ($keys_to_include) {
            return array_intersect_key($period, array_flip($keys_to_include));
        }, $periods);

        return json_encode($mapped_periods);
    }
}
