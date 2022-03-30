<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class GitHubDinoService
{
    public function getDinoIssues(): array
    {
        $client = HttpClient::create();
        $response = $client->request(method: 'GET', url: 'https://api.github.com/repos/jrushlow/nothing-here/issues');

        $dinoIssues = [];

        foreach ($response->toArray() as $issue) {
            $dinoIssues[] = [
                'title' => $issue['title'],
                'health' => $issue['labels'][0]['name']
            ];
        }

        return $dinoIssues;
    }
}
