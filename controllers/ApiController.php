<?php
declare(strict_types=1);

// JSON endpoints used by the live search (Fetch API)
class ApiController
{
    public function vehicles(): void
    {
        $rows = Vehicle::all(trim((string) ($_GET['q'] ?? '')), (string) ($_GET['status'] ?? ''));
        $this->json(['data' => $rows]);
    }

    public function customers(): void
    {
        $this->json(['data' => Customer::all(trim((string) ($_GET['q'] ?? '')))]);
    }

    private function json(array $payload, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
