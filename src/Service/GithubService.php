<?php

namespace App\Service;

use App\Enum\HealthStatus;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubService
{
    public function __construct(private HttpClientInterface $httpClient, private LoggerInterface $logger)
    {
    }

    public function getHealthReport(string $dinosaurName): HealthStatus
    {
        $health = HealthStatus::HEALTHY;

        $response = $this->httpClient->request(
            method: 'GET',
            url: 'https://api.github.com/repos/SymfonyCasts/dino-park/issues'
        );

        $this->logger->info('Request Dino Issues', [
            'dino' => $dinosaurName,
            'responseStatus' => $response->getStatusCode(),
        ]);

        foreach ($response->toArray() as $issue) {
            if (str_contains($issue['title'], $dinosaurName)) {
                $health = $this->getDinoStatusFromLabels($issue['labels']);
            }
        }

        return $health;
    }

    private function getDinoStatusFromLabels(array $labels): HealthStatus
    {
        $status = null;

        foreach ($labels as $label) {
            $label = $label['name'];

            // We only care about "Status" labels
            if (!str_starts_with($label, 'Status:')) {
                continue;
            }

            // Remove the "Status:" and whitespace from the label
            $status = trim(substr($label, strlen('Status:')));
        }

        return HealthStatus::tryFrom($status);
    }
}
