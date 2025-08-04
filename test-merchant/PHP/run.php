<?php

include "Paylabs.php";

$config = [
  'server' => 'SIT',
  'mid' => '010001',
  'version' => 'v2.1',
  'privateKey' => 'MERCHANT PRIVATE KEY',
  'publicKey' => 'PAYLABS PUBLIC KEY',
  'log' => false
];
$paylabs = new Paylabs($config);

// $mid = "010366"; //MERCHANT ID
// $server = "SIT"; //SIT & PROD
// $version = "v2.1";
// $privKey = "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCzwd34MKHBh+IoKStgc8b4EFjq/Sdk0GasntyIZ+lXheju+HEdw/7bUr5ktEzmGHLlI2Ic2Fol1Cd6oKdjfW6UBCj/GXsi26pOxwWGNCiVxKVJkakESDWog9pALPea2vVnpOL+jw5OXXmTbgUE9kvfPvRQXerYnnEEqNaQcp18dpMZ20KTo6HK4vvKszilplMs572aGFTqQF97SntNnPcR3PbnIeovIS3kQgpzu/mxJwQ9S2z0LL3kYNDTX8OKwC2qX31+RfHaNZhA4ojzdJY8kh/hlyP/Jc1PUZxEA84ua1OSThuuLOs7XXjFV4RfOmMcOw1VZb1ensTrOuIQ7ahnAgMBAAECggEANX2lPevK1rpG1Eib/dG2Da9fpl260UcqpoeHfJffWgohLKuFjaP7POy8d18n2vi/h+wlkAvZl5QwQ093QCXdN/Kc+HxupgrT/SlAPZVb1mV0AhZ0Qy/xt7m0wpAfBlfN2w+39KRBT5SFTxfYKofYQBCNxzSLY/aSDx7m6okQ0C5ARgktiPyHFU5RBWH5AmSUghU2t5umiO8T8LnvoYLBbDS39N5L6/Up60SScDmL5sXG3rJ+tjlwe+tgbkPJWVcpgxlzr8qJmXhYOjne1qElVjbul26KeyQ1+eXaL2wNEgSATYpFWlBp5an0nGhESC8WrsNijpvFPwwU4pQtIHLDIQKBgQDwxLBpYzzhFBzJfsnKWf6HY/40JBUHhqiBO9JARaTrZhnDXTMF7J+PIkPZhV/I21ZBGAo02JNWD6Itj8J9Yuuk6akXast3It+au4+3n4ZBi0gjTfuYxmEGk+pgjQ2EdWESp07weiuedOHK3qDE+YcmJQqRIhM7wCTq3tLxYIC7LwKBgQC/IRZS2L1IvJWLRjpdrBCCohznrL9AM7YnSdCzcBzDJUNKhCduiOeQoKunPwMBVoqrZ+vZjwjsDZyv5ZjQ5EXOy1UlB4D41sMoQR10xuVuZz//5/In7zJYOo1g+m5W+uPXFrOgBkYv17vW+8i47tTiUD252COKUxWJhQclh5o4SQKBgQC+PEq/GmAR31hXhMxgC/dmdoV2IURUa/tcES4xBlYmgMe+azdTaQExkPtXNWDXTSa7zAjUdIMFrtiPc1VSyQQzi0PFcuZMvcs7cRM/HNZxl2FqM8Wlkqk7D0hS/WA+1pfaq0NeEsnAK830Ay+4MwisBPxybCASR+FjiqfVfFB6lQKBgHZguOHhKgN2Ai179sRzwNoYLEz1upK6NJTK7D9nLflUCASMK5wToVD7orV4mpTmlAar2/Eo3T7PA4ASdX7UE8SMPZUsu53j7oXtJqBgXFPwETyf3WUMiqMIyO4KzfKDfECI6AIyQKB7PvVdhym7ixEM395OV1ziLtG5Gf8NqOA5AoGAUfEt0sIld/SB6VrDd6FGm2Ho7LcaAx3UJuFBepL/LfycvpDCz0p/4lomsjpoftBWA7I6X3h9LWA0hplMKTX816tVmKCs/x+WWvT7N8jTIYcFEobTi+2hVKxncrxQrwQCQVXBcJEYarrqRUGI8dDlYcg9Y+UfU8eA2ML4gltrvTE=";
// $pubKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsjufK6HhvscZEToMNygH2T95HfFrZ/14uPD4P/MnNhGaB6cOT5pwzbB+aNivZ5alSPXvlAWblzF1kcJGTSoh7CL+cyjfiFkFynD95qqJZxU3Qx1rfYzz5wuyQ7ytWDF8QG3gRQBpDl248FPXprM6VN//kriWeq7hGXw27h3TyAtcfd4kn3FHLxts6ZW3eyIbEjvIh6f+jtPePUbUpAMFG11/LKLI8xcYs5wgJc2qqwC9FXZB24sz/pvGJFAUD3HCBMA//FrU+lecGfk2n2uLFf/K4V5psuO8fEgoX7ZwE5blKwuRAVBBJZNiRPyIKtKK/vJRvu0rs1PQnbDsBrvwswIDAQAB";

// $paylabs->setMid($mid);
// $paylabs->setServer($server);
// $paylabs->setVersion($version);
// $paylabs->setPrivateKey($privKey);
// $paylabs->setPublicKey($pubKey);

// VALIDATE CALLBACK
// $paylabs->setPublicKey("MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsjufK6HhvscZEToMNygH2T95HfFrZ/14uPD4P/MnNhGaB6cOT5pwzbB+aNivZ5alSPXvlAWblzF1kcJGTSoh7CL+cyjfiFkFynD95qqJZxU3Qx1rfYzz5wuyQ7ytWDF8QG3gRQBpDl248FPXprM6VN//kriWeq7hGXw27h3TyAtcfd4kn3FHLxts6ZW3eyIbEjvIh6f+jtPePUbUpAMFG11/LKLI8xcYs5wgJc2qqwC9FXZB24sz/pvGJFAUD3HCBMA//FrU+lecGfk2n2uLFf/K4V5psuO8fEgoX7ZwE5blKwuRAVBBJZNiRPyIKtKK/vJRvu0rs1PQnbDsBrvwswIDAQAB");
// $json = '{"merchantId":"010366","requestId":"N01036620250523366000000261747998567809","errCode":"0","paymentType":"QRIS","amount":"50000.00","createTime":"20250523180824","successTime":"20250523180926","merchantTradeNo":"66-250523110817","platformTradeNo":"2025052336600000026","status":"02","paymentMethodInfo":{"phoneNumber":"00000","nmid":"123","payer":"paylabs","rrn":"66434160508792650818"},"productName":"Order #66 - Test aja"}';
// $sign = 'q6tP6dt3Jy2kBYbLbqjBl6ZGZdvX2pDMi5CQENNqPmbsp6hoizgpKvnaqLKRHxgWpI91qZv/yWGrSFGJRah8VACJusK9cQq/xGk7vvbciLP5+VUSQzY5CjhycEC4oGDMBTRn++tIVPNtgwvgdJrELglu7qmiAOORvosZ2b/cOaHtJ9RLyxOQBci02ffIf+L0eYx72pDuVB8jGC5kk3bW+ZoxTvMOKPxYxaph48N5rU7LDpxYazKS9hrZGEEfSKpAlK+wr4azaotoQJiW45Y9I4n1DcfgnOdvuhfjjmiMCGgmXZJRgVfWcbYMTzKnYsKro63IeGA9ovsYW0k2aw/Hbg==';
// $dateTime = "2025-05-23T18:09:27.812+07:00";
// $validasi = $paylabs->verifySign("/", $json, $sign, $dateTime);
// var_dump($validasi);
// die;

$json = '{"partnerServiceId":"010366","customerNo":"00000000000000000000","virtualAccountNo":"1467573987157514","virtualAccountName":"Vehicle Tax KT1020MT","trxId":"2025080413224755593520151","paymentRequestId":"2025080436600000131","channelCode":445,"paidAmount":{"value":"2631125.00","currency":"IDR"},"totalAmount":{"value":"2631125.00","currency":"IDR"},"trxDateTime":"2025-08-04T13:24:52+07:00","referenceNo":"2025080436600000131","additionalInfo":{"paymentType":"BCAVA","transFeeRate":"0.000000","vatFee":"0","totalTransFee":"2000.00","transFeeAmount":"2000.00"}}';
$sign = 'T0ZtZ+ERxV9UAuGLrDE8VaviUAp+VcEm/0UOVf5Ht90RMrxZEDSLVu8quP/lpIYNbBo3vuThFoG9nl9ShyvSf+dOpMVc8R0v3pLSgd6nFTsvyXDVjd5yeonmBNEsmzoTa8H2FtQEoPsQqYR8RQKlcAAKzB661TnAkkT0hAuU+PCExncN6l4AzfJHyd11kF5J5/rl+Y0jGvHV3FY+LKyHmMpW5wTjhVwrXrcxElGDHv+jm/ia8JdtM9EsPE1KaYsiyoE8fd3CdX5PQgSTTRI+yBYwHb+jy3nfZzJdxKbxBwuOW2lA9Mns9NBMy58+xxj92FK6mKEBM4x4yLe2oWZJ+Q==';
$dateTime = "2025-08-04T13:26:47+07:00";
$validasi = $paylabs->verifySignature("/transfer-va/payment", $json, $sign, $dateTime);
var_dump($validasi);

if ($validasi) {
  $paylabs->responseCallbackSnap(json_decode($json, true));
}
die;


// for ($i = 0; $i < 20; $i++) {

// $h5 = $paylabs->setH5("20000", "081234567890", "Testing", "https://google.com", "Test", null, "https://webhook.site/46e5ff8d-523c-4abb-8dc6-a5c14851d9b4");
// $qris = $paylabs->setQRIS("9000", "Sepeda");
// $wallet = $paylabs->setEMoney("LINKAJABALANCE", "10000", "Sepeda");
// $va = $paylabs->setVA("MaybankVA", "10000", "Sepeda", "Irfan");
// $cc = $paylabs->setCC("CreditCard", "11000", "Sepeda", "Irfan");
// $credit = $paylabs->setECredit("Indodana", "25000", "Sepeda");
$otc = $paylabs->setOTC("Indomaret", "25000", "Sepeda", "Irfan");
// $trx = $paylabs->request();

$payment = $paylabs->request();
var_dump($payment);
// }
