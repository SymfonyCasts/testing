<?php

namespace App\Service;

use App\Enum\HealthStatus;
use Symfony\Component\HttpClient\HttpClient;

class GithubService
{
    public function getHealthReport(string $dinosaurName): HealthStatus
    {
        $health = HealthStatus::HEALTHY;

        $client = HttpClient::create();

        $response = $client->request(
            method: 'GET',
            url: 'https://api.github.com/repos/SymfonyCasts/dino-park/issues'
        );

        foreach ($response->toArray() as $issue) {
            if (str_contains($issue['title'], $dinosaurName)) {

            }
        }

        return $health;
    }
}
