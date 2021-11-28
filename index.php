<?php

use App\API\WeatherAPI;

// Header constants.
//---------------------------------------------//.
const HEADER_STATUS_SUCCESS       = 'HTTP/1.1 200 OK';
const HEADER_STATUS_UNAUTHORIZED  = 'HTTP/1.1 401 Unauthorized';
const HEADER_STATUS_UNPROCESSABLE = 'HTTP/1.1 422 Unprocessable Entity';
const HEADER_STATUS_SERVER_ERROR  = 'HTTP/1.1 500 Internal Server Error';


// Require files and set headers.
//---------------------------------------------//.
require_once 'model/Database.php';
require_once 'model/ApiTokenModel.php';
require_once 'api/BaseAPI.php';
require_once 'api/WeatherAPI.php';

header("Access-Control-Allow-Origin: *");

// Handle Request
//---------------------------------------------//.
$api_token      = isset($_SERVER['HTTP_X_API_TOKEN']) ? $_SERVER['HTTP_X_API_TOKEN'] : null;
$endpoint       = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '/api/') + 1);
$endpoint_parts = explode('/', $endpoint);

$api_base       = isset($endpoint_parts[0]) ? $endpoint_parts[0] : null; // api.
$api_name       = isset($endpoint_parts[1]) ? $endpoint_parts[1] : null; // weather.
$api_location   = isset($endpoint_parts[2]) ? $endpoint_parts[2] : null; // office.
$api_action     = isset($endpoint_parts[3]) ? $endpoint_parts[3] : null; // forecast.

// gaurd: since we currently only have 1 endpoint, we make sure that endpoint is being requested
if ($api_base !== 'api' || $api_name !== 'weather' || $api_location !== 'office' || $api_action !== 'forecast') {
    header(HEADER_STATUS_UNPROCESSABLE);
    exit('Error: Unknown endpoint');
}

// gaurd: ensure token present.
if (!$api_token) {
    header(HEADER_STATUS_UNAUTHORIZED);
    exit('Error: Missing token');
}

$weather_api = new WeatherAPI($api_token);
$weather_api->call($api_action);

exit;
