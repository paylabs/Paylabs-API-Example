<?php

// namespace App\Libraries;

date_default_timezone_set('Asia/jakarta');

class Paylabs
{
    private $server = "SIT";
    private $mid;
    private $version;
    private $endpoint = "/api-pay/snap/";
    private $url_prod = "https://remit-api.paylabs.co.id/api-pay/snap/";
    private $url_sit = "https://sit-remit-api.paylabs.co.id/api-pay/snap/";
    private $log = false;
    private $privateKey;
    private $publicKey;
    private $date;
    private $idRequest;
    private $notifyUrl;
    private $signature;
    private $path;
    private $headers;
    private $body;
    private $customerReference;
    private $accountNo;
    private $bankName;
    private $bankCode;
    private $merchantTradeNo;

    public function __construct()
    {
        $this->date = date("Y-m-d") . "T" . date("H:i:s.B") . "+07:00";
        $this->idRequest = strval(date("YmdHis") . rand(11111, 99999));
        $this->merchantTradeNo = $this->idRequest;
    }

    public function setMid($mid)
    {
        $this->mid = $mid;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function setLog($log = false)
    {
        $this->log = $log;
    }

    public function setIdRequest($id)
    {
        $this->idRequest = $id;
    }

    public function setMercTradeNo($no)
    {
        $this->merchantTradeNo = $no;
    }

    public function setNotifyUrl($url)
    {
        $this->notifyUrl = $url;
    }

    public function setServer($server)
    {
        $this->server = $server;
    }

    public function setPrivateKey($private_key)
    {
        $this->privateKey = '-----BEGIN RSA PRIVATE KEY-----
' . $private_key . '
-----END RSA PRIVATE KEY-----';
    }

    public function setPublicKey($public_key)
    {
        $this->publicKey = '-----BEGIN PUBLIC KEY-----
' . $public_key . '
-----END PUBLIC KEY-----';
    }

    private function getUrl()
    {
        if ($this->server == "PROD") {
            return $this->url_prod . $this->version;
        }

        if ($this->server == "SIT") {
            return $this->url_sit . $this->version;
        }

        return $this->url_sit . $this->version;
    }

    private function getEndpoint()
    {
        return $this->endpoint . $this->version;
    }

    private function setHeaders()
    {
        $this->headers = array(
            'X-TIMESTAMP:' . $this->date,
            'X-SIGNATURE:' . $this->signature,
            'X-PARTNER-ID:' . $this->mid,
            'X-EXTERNAL-ID:' . $this->idRequest,
            'ORIGIN:www.paylabs.co.id',
            'Content-Type:application/json;charset=utf-8'
        );

        return $this->headers;
    }

    public function inquiry($bankNo, $bankCode, $remitType, $amount, $notifyUrl = null)
    {
        $this->path = "/account-inquiry-external";

        $this->body = [
            'partnerReferenceNo' => $this->idRequest,
            'beneficiaryAccountNo' => $bankNo,
            'beneficiaryBankCode' => $bankCode,
            'additionalInfo' => [
                "remitType" => $remitType,
                "amount" => $amount
            ]
        ];

        if (!is_null($notifyUrl)) {
            $this->notifyUrl = $notifyUrl;
        }

        return $this->request();
    }

    public function transfer($customerReference, $bankNo, $bankName, $bankCode, $remitType, $amount, $notifyUrl = null)
    {
        $this->path = "/transfer-interbank";
        $this->body = [
            'partnerReferenceNo' => $this->idRequest,
            'customerReference' => $customerReference,
            'beneficiaryAccountNo' => $bankNo,
            'beneficiaryAccountName' => $bankName,
            'beneficiaryBankCode' => $bankCode,
            'amount' => [
                'value' => $amount,
                'currency' => "Rp"
            ],
            'additionalInfo' => [
                'remitType' => $remitType,
                'notifyUrl' => $notifyUrl
            ]
        ];

        return $this->request();
    }

    public function status($partnerReferenceNo)
    {
        $this->path = "/transfer/status";
        $this->body = [
            'originalPartnerReferenceNo' => $partnerReferenceNo
        ];

        return $this->request();
    }

    public function balance()
    {
        $this->path = "/balance-inquiry";
        $this->body = [
            'partnerReferenceNo' => $this->idRequest,
            'accountNo' => $this->mid
        ];

        return $this->request();
    }

    public function displayLog($msg)
    {
        if ($this->log == true) {
            var_dump($msg);
            echo "<br><br>";
        }
    }

    private function generateSign()
    {
        $shaJson  = strtolower(hash('sha256', json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
        $signatureBefore = "POST:" . $this->getEndpoint() . $this->path . ":" . $shaJson . ":" . $this->date;
        $binary_signature = "";
        $this->displayLog($signatureBefore);

        $algo = OPENSSL_ALGO_SHA256;
        openssl_sign($signatureBefore, $binary_signature, $this->privateKey, $algo);

        $sign = base64_encode($binary_signature);
        $this->signature = $sign;
        return $sign;
    }

    public function verifySign($path, $dataToSign, $sign, $dateTime)
    {
        $binary_signature = base64_decode($sign);
        $shaJson  = strtolower(hash('sha256', $dataToSign));
        $signatureAfter = "POST:" . $path . ":" . $shaJson . ":" . $dateTime;
        $this->displayLog($signatureAfter);

        $validateKey = openssl_pkey_get_public($this->publicKey);
        if ($validateKey === false) {
            die("Error loading public key");
        }

        $algo =  OPENSSL_ALGO_SHA256;
        $verificationResult = openssl_verify($signatureAfter, $binary_signature, $this->publicKey, $algo);

        if ($verificationResult === 1) {
            return true;
        }
        return false;
    }

    private function request()
    {
        $this->generateSign();
        $this->setHeaders();

        $this->displayLog($this->getUrl());
        $this->displayLog(json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->displayLog(json_encode($this->headers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $this->post();
    }

    public function getTransferFile($partnerReferenceNo, $date)
    {
        $this->path = "/transfer-file";
        $this->body = [
            'partnerReferenceNo' => $partnerReferenceNo,
            'date' => $date
        ];

        return $this->get(); // metode GET dengan header + signature
    }

    private function get()
    {
        $this->generateSign();      // Buat X-SIGNATURE
        $this->setHeaders();        // Buat X-TIMESTAMP, X-SIGNATURE, dll
        $this->displayLog($this->getUrl());
        $this->displayLog(json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->displayLog(json_encode($this->headers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getUrl() . $this->path,
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => $this->headers,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $this->displayLog("RESPONSE: " . $response);
        return json_decode($response);
    }


    private function post()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getUrl() . $this->path,
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => $this->headers,
        ));
        $headerSent = curl_getinfo($curl, CURLINFO_HEADER_OUT);
        $information = curl_getinfo($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $response = curl_exec($curl);
        curl_close($curl);

        $this->displayLog($response);
        return json_decode($response);
    }


    public function responseCallback($path)
    {
        $this->path = $path;
        $this->body = array(
            "responseCode" => "2004300",
            "responseMessage" => "Request has been processed successfully"
        );

        $signature = $this->generateSign();

        // Set HTTP response headers
        header("Content-Type: application/json;charset=utf-8");
        header("X-TIMESTAMP: " . $this->date);
        header("X-SIGNATURE: " . $signature);
        header("X-PARTNER-ID: " . $this->mid);
        header("X-EXTERNAL-ID: " . $this->idRequest);

        // Encode the response as JSON and output it
        echo json_encode($this->body, JSON_UNESCAPED_UNICODE);
    }
}
