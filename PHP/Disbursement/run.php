<?php

include "Paylabs.php";

$paylabs = new Paylabs();

$mid = "010001"; //MERCHANT ID
$server = "SIT"; //SIT & PROD
$version = "v1.2";
$privKey = ""; //MERCHANT PRIVATE KEY
$pubKey = "";

$paylabs->setMid($mid);
$paylabs->setServer($server);
$paylabs->setVersion($version);
$paylabs->setPrivateKey($privKey);
$paylabs->setPublicKey($pubKey);
$paylabs->setLog(true);

$inquiry = $paylabs->inquiry("081234567890", "DANA", "wallet", 20000);
$transfer = $paylabs->transfer($inquiry->referenceNo, "081234567890", $inquiry->beneficiaryAccountName, "DANA", "wallet", 20000, "https://domain.com/callback.php");
$status = $paylabs->status($transfer->partnerReferenceNo);
$balance = $paylabs->balance();
$response = $paylabs->getTransferFile("20250414214559470", "2025-04-13");
print_r($response);

// VERIFY CALLBACK
$sign = "nAytsVfmquMVB10IDhMso0KqrEoyy5cqH9pNyBPOrbMePnA0W0fAuv6O/INI5bdd2CFjbubZxOfXhVRnzaiB5AwISTRHhMNRQXBO2f+32AXNqH+DxBlJTlEXnmQuUin/XCHCGMQJ7nFH1e/MDYfLiiPgnK17FryMGnPwGWlS4+gkPg3C5tBppCaLlP3vnGIrHoX965HfeJws4W9/nIzet7P6x5BWJ4Ihqz2L8wDm7DvGwDdl0b/eEjj3YNnPlA6YFyoS6oy0OLN4GaEUO/vbQA0fCQw1lxZpPhW+9A9WBscVUTvjb1M4XnwJeelk7ncygSoqmQBvdNG8Ui0C5qW4lw==";
$date = "2024-09-11T09:13:19.114Z";
$jsonBody = '{"beneficiaryAccountNo":"081234567890","amount":{"currency":"Rp","value":20000.00},"originalExternalId":"2024091116114876050","serviceCode":"18","transactionStatusDesc":"Successful","originalPartnerReferenceNo":"2024091116114876050","responseCode":"2004300","originalReferenceNo":"2024091136690000076","referenceNumber":"2024091116114876050","latestTransactionStatus":"00","additionalInfo":{"transFeeRate":0.0100,"totalTransFee":1700.00,"transFeeAmount":1500.00},"responseMessage":"Request has been processed successfully","sourceAccountNo":"010366"}';

$verify = $paylabs->verifySign("/disbursement/callback.php", $jsonBody, $sign, $date);
