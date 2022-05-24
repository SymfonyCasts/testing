<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class GithubService
{
    public function getHealthReports(array $dinosaurs): array
    {
        $client = HttpClient::create();

        $response = $client->request('GET', 'https://api.github.com/repos/SymfonyCasts/dino-park/issues');

        foreach ($response->toArray() as $issue) {
            foreach($dinosaurs as $dinosaur) {
                if (str_contains($issue['title'], $dinosaur->getName())) {
                    $dinosaur->setHealth($this->getDinoStatusFromLabels($issue['labels']));
                }
            }
        }

        return $dinosaurs;
    }

    private function getDinoStatusFromLabels(array $labels): string
    {
        return str_replace(
            search: 'STATUS: ',
            replace: '',
            subject: strtoupper($labels[0]['name'])
        );
    }
}
