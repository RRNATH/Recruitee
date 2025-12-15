<?php

class RecruiteeService
{
    private string $baseUrl;
    private string $companyId;
    private ?string $apiKey = null;

    /**
     * @param array $config Config array from config.php
     * @param string|null $apiKey Pre-resolved token
     */
    public function __construct(array $config, ?string $apiKey = null)
    {
        $this->baseUrl   = rtrim($config['recruitee_base_url'], '/');
        $this->companyId = $config['recruitee_company_id'];
        $this->apiKey    = $apiKey;
    }

    /**
     * Check if service is usable
     */
    public function hasApiKey(): bool
    {
        return !empty($this->apiKey);
    }

    /* ===================== PUBLIC METHODS ===================== */

    public function get(string $endpoint): array
    {
        return $this->request('GET', $endpoint);
    }

    public function post(string $endpoint, ?array $data = null): array
    {
        return $this->request('POST', $endpoint, $data);
    }

    public function patch(string $endpoint, ?array $data = null): array
    {
        return $this->request('PATCH', $endpoint, $data);
    }

    public function patchMultipart(string $endpoint, array $data): array
    {
        return $this->requestMultipart('PATCH', $endpoint, $data);
    }

    /* ===================== INTERNAL ===================== */

    private function request(string $method, string $endpoint, ?array $data = null): array
    {
        if (!$this->hasApiKey()) {
            return [
                'status' => 400,
                'body'   => json_encode(['error' => 'Missing or invalid API token']),
            ];
        }

        $url = $this->buildUrl($endpoint);
        $ch  = curl_init($url);

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
        ];

        if (in_array(strtoupper($method), ['POST', 'PATCH'], true)) {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data ?? []));
        }

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            return [
                'status' => 500,
                'body'   => json_encode(['error' => 'Curl error: ' . $error]),
            ];
        }

        curl_close($ch);

        return [
            'status' => $httpCode,
            'body'   => $response,
        ];
    }

    private function requestMultipart(string $method, string $endpoint, array $data): array
    {
        if (!$this->hasApiKey()) {
            return [
                'status' => 400,
                'body'   => json_encode(['error' => 'Missing or invalid API token']),
            ];
        }

        $url = $this->buildUrl($endpoint);
        $ch  = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            return [
                'status' => 500,
                'body'   => json_encode(['error' => 'Curl error: ' . $error]),
            ];
        }

        curl_close($ch);

        return [
            'status' => $httpCode,
            'body'   => $response,
        ];
    }

    private function buildUrl(string $endpoint): string
    {
        if (strpos($endpoint, '/c/' . $this->companyId) === 0) {
            return $this->baseUrl . $endpoint;
        }

        return $this->baseUrl
            . '/c/' . $this->companyId
            . '/' . ltrim($endpoint, '/');
    }
}
