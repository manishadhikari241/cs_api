<?php

namespace App\Utilities\APIs;

use GuzzleHttp\Client;

class InternalAPI
{
    private $client   = null;
    public $response  = null;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => getenv('APP_URL') . '/api/v1/'
        ]);
    }

    public function call(string $action = 'POST', string $endpoint = 'checkout', array $params = [])
    {
        $action         = strtolower($action);
        $response       = $this->client->$action($endpoint, ['form_params' => $params]);
        $this->response = json_decode($response->getBody());
        return $this;
    }
}
