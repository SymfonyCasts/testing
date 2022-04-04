<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {
    }

    public function getHealthReports(): array
    {
        $response = $this->httpClient->request('GET', 'https://api.github.com/repos/jrushlow/nothing-here/issues');

        $healthReports = [];

        foreach ($response->toArray() as $issue) {
            if (str_contains($issue['title'], 'Big Eaty')) {
                $healthReports[] = ['name' => 'Big Eaty', 'health' => $issue['labels'][0]['name']];
            }

            if (str_contains($issue['title'], 'Maverick')) {
                $healthReports[] = ['name' => 'Maverick', 'health' => $issue['labels'][0]['name']];
            }
        }

        return $healthReports;
    }
}
