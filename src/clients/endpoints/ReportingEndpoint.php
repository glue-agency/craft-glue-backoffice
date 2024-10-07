<?php

namespace GlueAgency\Backoffice\clients\endpoints;

use GlueAgency\Backoffice\clients\GlueClient;

class ReportingEndpoint
{

    protected GlueClient $client;

    public function __construct(GlueClient $client)
    {
        $this->client = $client;
    }

    public function report(array $data)
    {
        return $this->client->post('actions/backoffice/report', [
            'form_params' => $data,
        ]);
    }
}
