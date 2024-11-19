<?php

include "Paylabs.php";

$mid = "010001"; //MERCHANT ID
$server = "SIT"; //SIT & PROD
$version = "v2.1";
$privKey = ""; //MERCHANT PRIVATE KEY

// SET DATA
$paylabs = new Paylabs();
$paylabs->setMid($mid);
$paylabs->setServer($server);
$paylabs->setVersion($version);
$paylabs->setPrivateKey($privKey);
$paylabs->setPublicKey(""); // PAYLABS PUBLIC KEY
//$paylabs->setLog(true);

// GET DATA
$headers = $_SERVER;
$httpSign = isset($headers['HTTP_X_SIGNATURE']) ? $headers['HTTP_X_SIGNATURE'] : '';
$dateTime = isset($headers['HTTP_X_TIMESTAMP']) ? $headers['HTTP_X_TIMESTAMP'] : '';
$rawdata = file_get_contents('php://input');
$data = json_decode($rawdata, true);

// HANDLE CALLBACK SNAP
$validasi = $paylabs->verifySign("/transfer-va/payment", $rawdata, $httpSign, $dateTime);
$paylabs->responseCallbackSnap("/transfer-va/payment", $data);

// CALLBACK NON SNAP
$validasi = $paylabs->verifySign("/v1/paylabs/notification", $json, $sign, $dateTime);
if ($validasi) {
    $data = json_decode($json, true);
    $errCode = $data['errCode'];
    $status = $data['status'];
    if ($errCode == "0" && $status == "02") {
        //PAYMENT SUCCESS, YOU CAN UPDATE THE ORDER TO PAID

        $paylabs->responseCallback("/Welcome/Callback");
        die;
    }
}
die;

$mid = "010001"; //MERCHANT ID
$server = "SIT"; //SIT & PROD
$version = "v2.1";
$privKey = ""; //MERCHANT PRIVATE KEY

$paylabs->setMid($mid);
$paylabs->setServer($server);
$paylabs->setVersion($version);
$paylabs->setPrivateKey($privKey);
$paylabs->setLog(true);

// $h5 = $paylabs->setH5("15000", "081234567890", "Testing", "https://google.com", "Test");
$qris = $paylabs->setQRIS("15000", "Sepeda");
// $wallet = $paylabs->setEMoney("OVOBALANCE", "10000", "Sepeda");
// $va = $paylabs->setVA("MaybankVA", "10000", "Sepeda", "Irfan");
// $cc = $paylabs->setCC("CreditCard", "11000", "Sepeda", "Irfan");
// $credit = $paylabs->setECredit("Indodana", "25000", "Sepeda");
// $otc = $paylabs->setOTC("Indomaret", "25000", "Sepeda", "Irfan");

$payment = $paylabs->request();
var_dump($payment);
$inquiry = $paylabs->inquiry("/qris/query", $payment->merchantTradeNo, "QRIS");
var_dump($inquiry);
