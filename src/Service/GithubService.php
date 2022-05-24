<?php

namespace App\Service;

use App\Entity\Dinosaur;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GithubService
{
    public function __construct(private HttpClientInterface $httpClient, private LoggerInterface $logger)
    {
    }

    public function getHealthReports(array $dinosaurs): array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://api.github.com/repos/SymfonyCasts/dino-park/issues');
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
        $status = null;

        foreach ($labels as $label) {
            $label = strtoupper($label['name']);

            // We only care about "Status" labels
            if (!str_starts_with($label, 'STATUS:')) {
                continue;
            }

            // Remove the "Status:" and whitespace from the label
            $status = trim(substr($label, strlen('STATUS:')));

            // Determine if we know about the label - throw an exception is we don't
            if (!in_array($status, [Dinosaur::STATUS_SICK, Dinosaur::STATUS_HUNGRY, Dinosaur::STATUS_HEALTHY])) {
                throw new \RuntimeException(sprintf('%s is an unknown status label!', $label));
            }
        }

        return $status ?? Dinosaur::STATUS_HEALTHY;
    }
}
