<?php

include "Paylabs.php";

$paylabs = new Paylabs();

// VALIDATE CALLBACK
// $paylabs->setPublicKey("MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAm89foNjHYbx0/gdC1kuCCA8K7ut7iUzS5PNIlcxhkLfnKiuZuonFgF1eBfYJLa7ZWP+W3BoLGcXzFEKMAwycOvDFtG5dqYqghM550oUCrZqIOpD6CziMMf8nHbztrOJDg2/mHURHNfoEx6D5lIRqO9WGkD42I1jeL4qHMnfxKCFGFtRF8/BJqPbvRX4DKVs36DxAacpl901Hy6WG94R1I1g8fBVLIs9QQc5cWu04NepzeP2M32/EbySzl+fzR5z63qzabWSXetkA5l1XFReb3HunHtYrZ5Mrrn32L8mO5q+cj9miu9+cEcPr/xaTEKlwQ/HvRZ2YbRUDQvxciFsKtQIDAQAB");
// $json = '{"merchantId":"010418","requestId":"N01041820240708418000000541720426703270","errCode":"0","paymentType":"BCAVA","amount":"94000.00","createTime":"20240708151527","successTime":"20240708151629","merchantTradeNo":"10176-1720426526-58422","platformTradeNo":"2024070841800000054","status":"02","paymentMethodInfo":{"vaCode":"9999900000000006"},"productName":"Fomotoko"}';
// $sign = 'Pa08XYz+ktSvTjKwA0CT6D1o34VkmxFE+9ghhl19UEvXkeIU2SjJEHeyJbOEKqUeS30vx/q7+G2D114Fi5cGRe+tbEGkSkK5b016R0DTFrCDWWlRyW5XT8Lu/D59koN868Q1E8oD0ioUAlBJRz7NC9bJzwNBW6IlhiFzst7qTuN9r+U54KZOdjzBzJgbXDD0x/rpllhWLDvJnJDo3p2X9FeSjtCfziKRnJsh0JuucxweMaNL/Mk5Ncn2e6/ew8fVJiE9DA8OGzU1SDOPxj8F0eS8mKCCVqzNkgdGwxMO045cy6exiQHGQq4VsH3ma1fcG3jln7kka1bN+WYqbArKJQ==';
// $dateTime = '2024-07-08T15:18:23.270+07:00';
// $validasi = $paylabs->verifySign("/v1/paylabs/notification", $json, $sign, $dateTime);

// if ($validasi) {
//     $data = json_decode($json, true);
//     $errCode = $data['errCode'];
//     $status = $data['status'];
//     if ($errCode == "0" && $status == "02") {
//         //PAYMENT SUCCESS, YOU CAN UPDATE THE ORDER TO PAID

//         $paylabs->responseCallback("/Welcome/Callback");
//         die;
//     }
// }
// die;

$mid = "010366"; //MERCHANT ID
$server = "SIT"; //SIT & PROD
$version = "v2.1";
$privKey = "MIIExxxx"; //MERCHANT PRIVATE KEY

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
