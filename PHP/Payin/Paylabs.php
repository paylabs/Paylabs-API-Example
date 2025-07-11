<?php

class Paylabs
{
    private $server;
    private $mid;
    private $version;
    private $log;
    private $privateKey;
    private $publicKey;

    private $date;
    private $idRequest;
    private $merchantTradeNo;

    private $url = [
        "SIT" => "https://sit-pay.paylabs.co.id/payment/",
        "PROD" => "https://pay.paylabs.co.id/payment/"
    ];

    public function __construct(array $config = [])
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->date = date("Y-m-d") . "T" . date("H:i:s.B") . "+07:00";
        $this->idRequest = strval(date("YmdHis") . rand(11111, 99999));
        $this->merchantTradeNo = $this->idRequest;

        $this->server = $config['server'] ?? 'SIT';
        $this->mid = $config['mid'] ?? null;
        $this->version = $config['version'] ?? null;
        $this->log = $config['log'] ?? false;

        if (!empty($config['privateKey'])) {
            $this->setPrivateKey($config['privateKey']);
        }
        if (!empty($config['publicKey'])) {
            $this->setPublicKey($config['publicKey']);
        }
    }

    public function setPrivateKey($key)
    {
        $this->privateKey = "-----BEGIN RSA PRIVATE KEY-----\n{$key}\n-----END RSA PRIVATE KEY-----";
    }

    public function setPublicKey($key)
    {
        $this->publicKey = "-----BEGIN PUBLIC KEY-----\n{$key}\n-----END PUBLIC KEY-----";
    }

    private function getFullUrl($path)
    {
        $base = $this->url[$this->server] ?? $this->url["SIT"];
        return $base . $this->version . $path;
    }

    private function buildHeaders($body, $path)
    {
        $signature = $this->generateSignature($body, $path);
        return [
            'X-TIMESTAMP: ' . $this->date,
            'X-SIGNATURE: ' . $signature,
            'X-PARTNER-ID: ' . $this->mid,
            'X-REQUEST-ID: ' . $this->idRequest,
            'Content-Type: application/json;charset=utf-8'
        ];
    }

    private function generateSignature($body, $path)
    {
        $hash = strtolower(hash('sha256', json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
        $data = "POST:/payment/{$this->version}{$path}:{$hash}:{$this->date}";

        if ($this->log) var_dump($data);

        openssl_sign($data, $binary, $this->privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($binary);
    }

    public function request($path, array $body)
    {
        // Set default fields
        $default = [
            'merchantId' => $this->mid,
            'requestId' => $this->idRequest,
            'merchantTradeNo' => $this->merchantTradeNo
        ];

        $payload = array_merge($default, $body);

        $headers = $this->buildHeaders($payload, $path);

        if ($this->log) {
            var_dump($this->getFullUrl($path));
            var_dump($payload);
            var_dump($headers);
        }

        // Send request
        $ch = curl_init($this->getFullUrl($path));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => $headers
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function verifySignature($path, $bodyRaw, $signature, $timestamp)
    {
        // Hash body
        $hash = strtolower(hash('sha256', $bodyRaw));

        // Build string to verify
        $data = "POST:{$path}:{$hash}:{$timestamp}";

        if ($this->log) {
            var_dump("String to verify:", $data);
        }

        // Decode signature from base64
        $binarySignature = base64_decode($signature);

        // Load public key
        $publicKey = openssl_pkey_get_public($this->publicKey);
        if (!$publicKey) {
            throw new Exception("Error loading public key");
        }

        // Verify signature
        $verify = openssl_verify($data, $binarySignature, $publicKey, OPENSSL_ALGO_SHA256);

        return $verify === 1;
    }

    public function responseCallback($path)
    {
        $payload = [
            'merchantId' => $this->mid,
            'requestId' => $this->idRequest,
            'errCode' => '0'
        ];

        // Generate signature
        $signature = $this->generateSignature($payload, $path);

        // Set HTTP headers
        header('Content-Type: application/json;charset=utf-8');
        header('X-TIMESTAMP: ' . $this->date);
        header('X-SIGNATURE: ' . $signature);
        header('X-PARTNER-ID: ' . $this->mid);
        header('X-REQUEST-ID: ' . $this->idRequest);

        // Return JSON
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}
