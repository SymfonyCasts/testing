<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class GitHubDinoService
{
    public function getDinoIssues(): array
    {
        $client = HttpClient::create();
        $response = $client->request(method: 'GET', url: 'https://api.github.com/repos/SymfonyCasts/reset-password-bundle/issues?per_page=1');

        return $response->toArray();
    }
}
