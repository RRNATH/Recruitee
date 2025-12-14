<?php

class RecruiteeService
{
    private string $baseUrl;
    private string $companyId;
    private string $apiKey;

    public function __construct(array $config)
    {
        $this->baseUrl = rtrim($config['recruitee_base_url'], '/');
        $this->companyId = $config['recruitee_company_id'];
        $this->apiKey = $config['recruitee_api_token'];
    }

    /**
     * Send GET request
     * @param string $endpoint
     * @return array ['status' => int, 'body' => string]
     */
    public function get(string $endpoint): array
    {
        return $this->request('GET', $endpoint);
    }

    /**
     * Send POST request
     * @param string $endpoint
     * @param array|null $data
     * @return array
     */
    public function post(string $endpoint, ?array $data = null): array
    {
        return $this->request('POST', $endpoint, $data);
    }

    /**
     * Send multipart/form-data PATCH (file upload)
     * @param string $endpoint
     * @param array $data
     * @return array ['status' => int, 'body' => string]
    */
    public function patchMultipart(string $endpoint, array $data): array
    {
        $url = $this->buildUrl($endpoint);
        error_log("Request recruitee upload cv url: $url");

        $ch = curl_init($url);

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            // IMPORTANT: do NOT set Content-Type manually
        ];

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS    => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER    => $headers,
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

    /**
     * Send PATCH request
     * @param string $endpoint
     * @param array|null $data
     * @return array
     */
    public function patch(string $endpoint, ?array $data = null): array
    {
        return $this->request('PATCH', $endpoint, $data);
    }

    /**
     * Generalized cURL request
     * @param string $method
     * @param string $endpoint
     * @param array|null $data
     * @return array
     */
    private function request(string $method, string $endpoint, ?array $data = null): array
    {
        $url = $this->buildUrl($endpoint);

        $ch = curl_init($url);

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
        ];

        $method = strtoupper($method);

        if (in_array($method, ['POST', 'PATCH'])) {
            $payload = json_encode($data ?? []);
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'status' => 500,
                'body' => json_encode(['error' => 'Curl error: ' . $error])
            ];
        }

        curl_close($ch);

        return [
            'status' => $httpCode,
            'body' => $response
        ];
    }

    /**
     * Build full URL for Recruitee API
     * @param string $endpoint
     * @return string
     */
    private function buildUrl(string $endpoint): string
    {
        // Automatically prepend "/c/{companyId}" if not already present
        if (strpos($endpoint, '/c/' . $this->companyId) === 0) {
            return $this->baseUrl . $endpoint;
        }
        return $this->baseUrl . '/c/' . $this->companyId . '/' . ltrim($endpoint, '/');
    }
}
