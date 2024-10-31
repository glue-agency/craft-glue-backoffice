<?php

namespace GlueAgency\Backoffice\clients\endpoints;

use GlueAgency\Backoffice\clients\GlueClient;

class ReportingEndpoint
{

    /**
     * @var GlueClient
     */
    protected $client;

    /**
     * @param GlueClient $client
     */
    public function __construct(GlueClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function report(array $data)
    {
        return $this->client->post('actions/backoffice/report', [
            'form_params' => $data,
        ]);
    }
}
