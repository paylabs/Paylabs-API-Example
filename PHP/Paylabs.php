<?php

// namespace App\Libraries;

date_default_timezone_set('Asia/jakarta');

class Paylabs
{
    public $server = "SIT";
    public $mid;
    public $version;
    public $endpoint = "/payment/";
    public $url_prod = "https://pay.paylabs.co.id/payment/";
    public $url_sit = "https://sit-pay.paylabs.co.id/payment/";
    public $log = false;
    public $privateKey;
    public $publicKey;
    public $date;
    public $idRequest;
    public $merchantTradeNo;
    public $notifyUrl;
    public $signature;
    public $path;
    public $headers;
    public $body;

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
            'X-REQUEST-ID:' . $this->idRequest,
            'Content-Type:application/json;charset=utf-8'
        );

        return $this->headers;
    }

    public function setH5($amount, $phoneNumber, $product, $redirectUrl, $payer = "Testing", $storeId = null, $notifyUrl = null)
    {
        $this->path = "/h5/createLink";
        $this->body = [
            'merchantId' => $this->mid,
            'merchantTradeNo' => $this->merchantTradeNo,
            'requestId' => $this->idRequest,
            'amount' => $amount,
            'phoneNumber' => $phoneNumber,
            'productName' => $product,
            'redirectUrl' => $redirectUrl,
            'payer' => $payer
        ];

        if (!is_null($storeId)) {
            $this->body['storeId'] = strval($storeId);
        }
        if (!is_null($notifyUrl)) {
            $this->body['notifyUrl'] = $notifyUrl;
        }

        return $this->body;
    }

    public function setQRIS($amount, $product, $storeId = null, $notifyUrl = null, $expired = null)
    {
        $this->path = "/qris/create";
        $this->body = [
            'merchantId' => $this->mid,
            'merchantTradeNo' => $this->merchantTradeNo,
            'requestId' => $this->idRequest,
            'paymentType' => "QRIS",
            'amount' => $amount,
            'productName' => $product
        ];

        if (!is_null($storeId)) {
            $this->body['storeId'] = strval($storeId);
        }
        if (!is_null($notifyUrl)) {
            $this->body['notifyUrl'] = $notifyUrl;
        }
        if (!is_null($expired)) {
            $this->body['expire'] = $expired;
        }

        return $this->body;
    }

    public function setEMoney($channel, $amount, $product, $storeId = null, $notifyUrl = null)
    {
        $this->path = "/ewallet/create";
        $this->body = [
            'merchantId' => $this->mid,
            'merchantTradeNo' => $this->merchantTradeNo,
            'requestId' => $this->idRequest,
            'paymentType' => $channel,
            'amount' => $amount,
            'productName' => $product
        ];

        if (!is_null($storeId)) {
            $this->body['storeId'] = strval($storeId);
        }
        if (!is_null($notifyUrl)) {
            $this->body['notifyUrl'] = $notifyUrl;
        }

        return $this->body;
    }

    public function setVA($channel, $amount, $product, $payer, $storeId = null, $notifyUrl = null)
    {
        $this->path = "/va/create";
        $this->body = [
            'merchantId' => $this->mid,
            'merchantTradeNo' => $this->merchantTradeNo,
            'requestId' => $this->idRequest,
            'paymentType' => $channel,
            'amount' => $amount,
            'productName' => $product,
            'payer' => $payer
        ];

        if (!is_null($storeId)) {
            $this->body['storeId'] = strval($storeId);
        }
        if (!is_null($notifyUrl)) {
            $this->body['notifyUrl'] = $notifyUrl;
        }

        return $this->body;
    }

    public function setCC($channel, $amount, $product, $payer, $storeId = null, $notifyUrl = null)
    {
        $this->path = "/cc/create";
        $this->body = [
            'merchantId' => $this->mid,
            'merchantTradeNo' => $this->merchantTradeNo,
            'requestId' => $this->idRequest,
            'paymentType' => $channel,
            'amount' => $amount,
            'productName' => $product
        ];

        if (!is_null($storeId)) {
            $this->body['storeId'] = strval($storeId);
        }
        if (!is_null($notifyUrl)) {
            $this->body['notifyUrl'] = $notifyUrl;
        }

        return $this->body;
    }

    public function setECredit($channel, $amount, $product, $storeId = null, $notifyUrl = null)
    {
        $this->path = "/dd/create";
        $this->body = [
            'merchantId' => $this->mid,
            'merchantTradeNo' => $this->merchantTradeNo,
            'requestId' => $this->idRequest,
            'paymentType' => $channel,
            'amount' => $amount,
            'productName' => $product
        ];

        if (!is_null($storeId)) {
            $this->body['storeId'] = strval($storeId);
        }
        if (!is_null($notifyUrl)) {
            $this->body['notifyUrl'] = $notifyUrl;
        }

        return $this->body;
    }

    public function setOTC($channel, $amount, $product, $payer, $storeId = null, $notifyUrl = null)
    {
        $this->path = "/store/create";
        $this->body = [
            'merchantId' => $this->mid,
            'merchantTradeNo' => $this->merchantTradeNo,
            'requestId' => $this->idRequest,
            'paymentType' => $channel,
            'amount' => $amount,
            'productName' => $product,
            'payer' => $payer,
        ];

        if (!is_null($storeId)) {
            $this->body['storeId'] = strval($storeId);
        }
        if (!is_null($notifyUrl)) {
            $this->body['notifyUrl'] = $notifyUrl;
        }

        return $this->body;
    }

    public function displayLog($msg)
    {
        if ($this->log == true) {
            var_dump($msg);
            echo "<br><br>";
        }
    }

    public function generateSign()
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

    public function request()
    {
        $this->generateSign();
        $this->setHeaders();

        $this->displayLog($this->getUrl());
        $this->displayLog(json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->displayLog(json_encode($this->headers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $this->post();
    }

    public function post()
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
        return json_decode($response);
    }

    public function inquiry($path, $merchantTradeNo, $paymentType, $storeId = null)
    {
        $this->idRequest = uniqid();
        $this->path = $path;
        $this->body = ["requestId" => $this->idRequest, "merchantId" => $this->mid, "merchantTradeNo" => $merchantTradeNo, "paymentType" => $paymentType];
        if (!is_null($storeId)) {
            $this->body['storeId'] = strval($storeId);
        }

        $this->generateSign();
        $this->setHeaders();

        $this->displayLog($this->getUrl());
        $this->displayLog(json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->displayLog(json_encode($this->headers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $this->post();
    }

    public function responseCallback($path)
    {
        $this->path = $path;
        $this->body = array(
            "merchantId" => $this->mid,
            "requestId" => $this->idRequest,
            "errCode" => "0"
        );

        $signature = $this->generateSign();

        // Set HTTP response headers
        header("Content-Type: application/json;charset=utf-8");
        header("X-TIMESTAMP: " . $this->date);
        header("X-SIGNATURE: " . $signature);
        header("X-PARTNER-ID: " . $this->mid);
        header("X-REQUEST-ID: " . $this->idRequest);

        // Encode the response as JSON and output it
        echo json_encode($this->body, JSON_UNESCAPED_UNICODE);
    }
}
