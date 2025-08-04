<?php

include "Paylabs.php";

$config = [
  'server' => 'PROD',
  'mid' => '010001',
  'version' => 'v2.1',
  'privateKey' => 'MERCHANT PRIVATE KEY',
  'publicKey' => 'MERCHANT PUBLIC KEY',
  'log' => false
];
$paylabs = new Paylabs($config);

$qris = $paylabs->request('/qris/create', [
  'paymentType' => 'QRIS',
  'amount' => '15000',
  'productName' => 'Sepeda',
  'notifyUrl' => 'https://webhook.site'
]);

var_dump($qris);

// // GET DATA
// $headers = $_SERVER;
// $httpSign = isset($headers['HTTP_X_SIGNATURE']) ? $headers['HTTP_X_SIGNATURE'] : '';
// $dateTime = isset($headers['HTTP_X_TIMESTAMP']) ? $headers['HTTP_X_TIMESTAMP'] : '';
// $rawdata = file_get_contents('php://input');
// $data = json_decode($rawdata, true);


// // HANDLE CALLBACK SNAP
// $json = '{"partnerServiceId":"010366","customerNo":"00000000000000000000","virtualAccountNo":"1467573987157514","virtualAccountName":"Vehicle Tax KT1020MT","trxId":"2025080413224755593520151","paymentRequestId":"2025080436600000131","channelCode":445,"paidAmount":{"value":"2631125.00","currency":"IDR"},"totalAmount":{"value":"2631125.00","currency":"IDR"},"trxDateTime":"2025-08-04T13:24:52+07:00","referenceNo":"2025080436600000131","additionalInfo":{"paymentType":"BCAVA","transFeeRate":"0.000000","vatFee":"0","totalTransFee":"2000.00","transFeeAmount":"2000.00"}}';
// $sign = 'T0ZtZ+ERxV9UAuGLrDE8VaviUAp+VcEm/0UOVf5Ht90RMrxZEDSLVu8quP/lpIYNbBo3vuThFoG9nl9ShyvSf+dOpMVc8R0v3pLSgd6nFTsvyXDVjd5yeonmBNEsmzoTa8H2FtQEoPsQqYR8RQKlcAAKzB661TnAkkT0hAuU+PCExncN6l4AzfJHyd11kF5J5/rl+Y0jGvHV3FY+LKyHmMpW5wTjhVwrXrcxElGDHv+jm/ia8JdtM9EsPE1KaYsiyoE8fd3CdX5PQgSTTRI+yBYwHb+jy3nfZzJdxKbxBwuOW2lA9Mns9NBMy58+xxj92FK6mKEBM4x4yLe2oWZJ+Q==';
// $dateTime = "2025-08-04T13:26:47+07:00";
// $validasi = $paylabs->verifySignature("/transfer-va/payment", $json, $sign, $dateTime);
// var_dump($validasi);

// if ($validasi) {
//   $paylabs->responseCallbackSnap(json_decode($json, true));
// }
// die;

// CALLBACK NON SNAP
// $validasi = $paylabs->verifySignature("/callback/paylabs", $json, $sign, $dateTime);
// if ($validasi) {
//     $data = json_decode($json, true);
//     $errCode = $data['errCode'];
//     $status = $data['status'];
//     if ($errCode == "0" && $status == "02") {
//         //PAYMENT SUCCESS, YOU CAN UPDATE THE ORDER TO PAID

//         $paylabs->responseCallback("/callback/paylabs");
//         die;
//     }
// }
// die;
