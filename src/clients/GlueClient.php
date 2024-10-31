<?php

namespace GlueAgency\Backoffice\clients;

use Craft;
use GlueAgency\Backoffice\Backoffice;
use GlueAgency\Backoffice\clients\endpoints\ReportingEndpoint;
use GuzzleHttp\Client;

class GlueClient
{

    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->client = $this->buildClient();
    }

    /**
     * @return ReportingEndpoint
     */
    public function reporting()
    {
        return new ReportingEndpoint($this);
    }

    /**
     * @return Client
     */
    protected function buildClient()
    {
        $settings = Backoffice::getInstance()->getSettings();

        return Craft::createGuzzleClient([
            'base_uri' => $settings->url,
            'headers'  => [
                'Accept' => 'application/json',
            ],
            'query'    => [
                'api_token' => $settings->token,
            ],
        ]);
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->client, $name], $arguments);
    }
}
