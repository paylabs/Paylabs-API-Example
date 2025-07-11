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
// $validasi = $paylabs->verifySign("/transfer-va/payment", $rawdata, $httpSign, $dateTime);
// $paylabs->responseCallbackSnap("/transfer-va/payment", $data);

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
