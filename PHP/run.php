<?php

include "Paylabs.php";

$paylabs = new Paylabs();

// VALIDATE CALLBACK
// $paylabs->setPublicKey("MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsjufK6HhvscZEToMNygH2T95HfFrZ/14uPD4P/MnNhGaB6cOT5pwzbB+aNivZ5alSPXvlAWblzF1kcJGTSoh7CL+cyjfiFkFynD95qqJZxU3Qx1rfYzz5wuyQ7ytWDF8QG3gRQBpDl248FPXprM6VN//kriWeq7hGXw27h3TyAtcfd4kn3FHLxts6ZW3eyIbEjvIh6f+jtPePUbUpAMFG11/LKLI8xcYs5wgJc2qqwC9FXZB24sz/pvGJFAUD3HCBMA//FrU+lecGfk2n2uLFf/K4V5psuO8fEgoX7ZwE5blKwuRAVBBJZNiRPyIKtKK/vJRvu0rs1PQnbDsBrvwswIDAQAB");
// $json = '{"merchantId":"010366","requestId":"N01036620240604366000000211717484634765","errCode":"0","paymentType":"MaybankVA","amount":"15000.00","createTime":"20240604140200","successTime":"20240604140301","merchantTradeNo":"2024060414015445005","platformTradeNo":"2024060436600000021","status":"02","paymentMethodInfo":{"vaCode":"9999900000000006"},"productName":"Product 1","transFeeRate":"0.000000","transFeeAmount":"5000.00","totalTransFee":"5000.00"}';
// $sign = 'rgX/D59369OFD7BV5ONQ8FII4FpbWAhKjsBNcjViFKLsgZr/mcuuWRh3sE5j4USgxHp2Vzmfl3Kmk1kdta+kpIV+XwXn8Z2Cox/AybnPJgiQWeaKrKmVzX/Ys5/1soRm+7ze8YhfIOWHgxRiIuCD3a+RGiIBcIedQ0QXSLnST3iYrjrkvCcJ+SslGM7S4efn4SYxYOI6OXWUSW8XETsNkOXIEzJLl7avFIHdlYOpRzTSXXzFdZCq3ydcEf5ZKs5BPUl2jlxEuJ8LX/e6e/NpW6kXPANf4sDaEuB5TBDRypxuNg51nNpy9yjG85dZOOSNDxWwiwevglhG/m3i3AlixQ==';
// $dateTime = '2024-06-04T14:03:54.766+07:00';
// $validasi = $paylabs->verifySign("/callback", $json, $sign, $dateTime);
// var_dump($validasi);

$mid = "010001"; //MERCHANT ID
$server = "SIT"; //SIT & PROD
$version = "v2.1";
$privKey = ""; //MERCHANT PRIVATE KEY

$paylabs->setMid($mid);
$paylabs->setServer($server);
$paylabs->setVersion($version);
$paylabs->setPrivateKey($privKey);

// $h5 = $paylabs->setH5("15000", "081234567890", "Testing", "https://google.com", "Test");
$qris = $paylabs->setQRIS("10000", "Sepeda", null, null, 3600);
// $wallet = $paylabs->setEMoney("OVOBALANCE", "10000", "Sepeda");
// $va = $paylabs->setVA("MaybankVA", "10000", "Sepeda", "Irfan");
// $cc = $paylabs->setCC("CreditCard", "11000", "Sepeda", "Irfan");
// $credit = $paylabs->setECredit("Indodana", "25000", "Sepeda", "Irfan");
// $otc = $paylabs->setOTC("Indomaret", "25000", "Sepeda", "Irfan");
// $trx = $paylabs->request();

$payment = $paylabs->request();
var_dump($payment);
