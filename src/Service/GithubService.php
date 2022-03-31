<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class GithubService
{
    public function getHealthReports(): array
    {
        $client = HttpClient::create();

        $response = $client->request('GET', 'https://api.github.com/repos/jrushlow/nothing-here/issues');

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
