<?php

namespace App\Api;

use \App\Model\ApiTokenModel;

class BaseApi
{
    public $api_token;
    protected ApiTokenModel $token_model;

    public function __construct(string $api_token)
    {
        $this->api_token = $api_token;
        $this->token_model = new ApiTokenModel();
    }

    /**
     * Calls API action method: $this->action_{$action_method}()
     */
    public function call(string $action_method, bool $require_valid_token = true): void
    {
        $api_method     = "action_$action_method";
        $is_valid_token = $this->token_model->is_valid($this->api_token);

        // respond unauthorized if required token is invalid
        if ($require_valid_token && !$is_valid_token) {
            $this->send_response([HEADER_STATUS_UNAUTHORIZED], 'Invalid API Token');
        }

        // respond unprocessable if calling unknown method
        if (!method_exists($this, $api_method)) {
            $this->send_response([HEADER_STATUS_UNPROCESSABLE], 'Call to unknown method');
        }

        try {
            //respond success with resulting data and increment api usage.
            $result = $this->{$api_method}();
            $this->send_response([HEADER_STATUS_SUCCESS, 'Content-Type: application/json'], $result, false);
            $this->token_model->record_use($this->api_token);
        } catch (\Exception $e) {
            // respond server error if exception was thrown while calling action method
            $this->send_response([HEADER_STATUS_SERVER_ERROR], $e->getMessage());
        }
        exit;
    }

    /**
     * Echos response to API call
     */
    protected function send_response(array $headers, string $data, bool $exit = true): void
    {
        foreach ($headers as $header) {
            header($header);
        }

        echo $data;

        if ($exit) {
            exit;
        }
    }
}
