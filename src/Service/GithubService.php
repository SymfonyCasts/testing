<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GithubService
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function getHealthReports(array $dinosaurs): array
    {
        $client = HttpClient::create();

        try {
            $response = $client->request('GET', 'https://api.github.com/repos/SymfonyCasts/dino-park/issues');
        } catch (TransportExceptionInterface $exception) {
            $this->logger->info($exception->getMessage());
        }

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
