<?php

namespace GlueAgency\Backoffice\clients;

use Craft;
use GlueAgency\Backoffice\Backoffice;
use GlueAgency\Backoffice\clients\endpoints\ReportingEndpoint;
use GuzzleHttp\Client;

class GlueClient
{

    protected Client $client;

    public function __construct()
    {
        $this->client = $this->buildClient();
    }

    public function reporting(): ReportingEndpoint
    {
        return new ReportingEndpoint($this);
    }

    protected function buildClient(): Client
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

    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->client, $name], $arguments);
    }
}
