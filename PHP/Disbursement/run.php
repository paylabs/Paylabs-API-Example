<?php

include "Paylabs.php";

$paylabs = new Paylabs();

$mid = "010366"; //MERCHANT ID
$server = "SIT"; //SIT & PROD
$version = "v1.2";
$privKey = "MIIEowIBAAKCAQEAgb5E5RCXHAtg8WvpGMV+9ouOPLl8FU89VOCc1Di7c7SCEHmFgKjUOitN8iQFrIlNkG2pyKOlD+X3UIY31R2WhOfHD+DcpcrKcVuxXHm+cEg9tFmuGy/xfVWRJf8AkJRgUADnPFHGaL89K0uF0sIXPeOq1ZGaYbMUYqtU1uS/JWY1Vai7TcoblhtDdQ5WMU49CgRcdIOO8lUjkTjjY1ci27duwwt0E9q1JMHjTpa1RHyh66PDfNTj5QlFm1hWhv6vzZC2lmKeyHDLEWoJFYTq+/FS3Oyg+kyjHI3crLPhaIzkl128FTaXvUWuR/6z30vWSXATzwwXP+7SiB3UirgtRQIDAQABAoIBADMQ88cBL6jfJaoAYj/YxC9AIJzwGNG+XZKB0diJ9+YDv0nCuvQ3/0hh8Q0EdqNBa6EUOMZ6+qX2StCL83/TZIItRCGQzOgImIPjpjySwo0pMfhBcBDNXwyI2FePNeayL/JtWJZaTXC/DNF0qViaAlHewUfMRmp79OMdMohegomySa55vAssLFNEi+vHq15wGMt9d1aLoynuVHxPGZ50KtgyjQunA7A0zyg5f8lFm28v7xo8qU8zr5rR+G0+wtsJPjnxoUT/st8TbbOfFYu75w83ss5exzr0MgQmglK6OmxHxdVTgwuO4d9C3I65mssrgGJkNJV0EQhJ/rFYNWxG1GUCgYEA9+dpocHGYuUgcYadSjWGDV1LQsfY5eWVSev3dtZnyRfVpVZO6gmHL2OmUWtunpzFptxbc/6I58vOVJdZikZs2JTPKBauj6Smci7crJMyR3wuICF6ArbStkl//Saw/lFsYmoR0gFD06bIa687OU5ffockjGNMXrUxZ7LhV5F+aWsCgYEAhfr69q70ykr31LeIr21uf0Cu8wdbKKl9lK82vv85/Tdq/8xtTy+59WTDuAyHDFDh67R3ci40+NQSP7vc5SUHSx32LHghoDeMA6TN/1IbCMbDmtaqIR6XzD/FVLp0zszAZkI0LnJKWDVJkNMyZhCEsl6tjXVUVYC3S0y8rJDwAA8CgYEA21VjoDpRzC3tBoSTpZS1hh2E+RDYVo9KBp0/1WTdbo4n+KDkMS087jC8dk0XEj6ioX9VastJVcx8QVunXS/yHa2Lm2x2BaEnot7TX9zcH2M/bC4yRTR8OcvN8azJ7DkeK/Ssz6FO4XQu3xeqzokI9GmdbJhueVzW2Wjq9w9DSQECgYA999+ryTAfgJ0wHdNykELTSK+iaHyZSgtzgbbokFPZ8o/i0EKepYx1G64KqoCsCZz0z/uPLCAEFtJ5+AIrWf0NmUYLO2USHZ788HT26prmbEh8jV0TBHthVP2IOtVb8QfsRCKueN45/iuQeJ6O5oT5myDalLH0+hvNECkUB9V5sQKBgBLY+2ImpQLbTJ3/Rg8eqTjGA7+WN4EKwFPiW3Krbj7urWko9G2OTz9WvJ+LX8YsrlH+PkSSvEpnzWuaBNREnfCTzmguSXkpz0jxQcoCPR7ufZ6x+JhmYnBSBwesWykBUhF8z4PKu5bPN0f4HrhPicJPNmnluJ5dRFBJbixr9Ort"; //MERCHANT PRIVATE KEY
$pubKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsjufK6HhvscZEToMNygH2T95HfFrZ/14uPD4P/MnNhGaB6cOT5pwzbB+aNivZ5alSPXvlAWblzF1kcJGTSoh7CL+cyjfiFkFynD95qqJZxU3Qx1rfYzz5wuyQ7ytWDF8QG3gRQBpDl248FPXprM6VN//kriWeq7hGXw27h3TyAtcfd4kn3FHLxts6ZW3eyIbEjvIh6f+jtPePUbUpAMFG11/LKLI8xcYs5wgJc2qqwC9FXZB24sz/pvGJFAUD3HCBMA//FrU+lecGfk2n2uLFf/K4V5psuO8fEgoX7ZwE5blKwuRAVBBJZNiRPyIKtKK/vJRvu0rs1PQnbDsBrvwswIDAQAB";

$paylabs->setMid($mid);
$paylabs->setServer($server);
$paylabs->setVersion($version);
$paylabs->setPrivateKey($privKey);
$paylabs->setPublicKey($pubKey);
$paylabs->setLog(true);

$inquiry = $paylabs->inquiry("081234567890", "DANA", "wallet", 20000);
$transfer = $paylabs->transfer($inquiry->referenceNo, "081234567890", $inquiry->beneficiaryAccountName, "DANA", "wallet", 20000, "https://waaa.irfan.co.id/disbursement/callback.php");
$status = $paylabs->status($transfer->partnerReferenceNo);
$balance = $paylabs->balance();


// VERIFY CALLBACK
$sign = "nAytsVfmquMVB10IDhMso0KqrEoyy5cqH9pNyBPOrbMePnA0W0fAuv6O/INI5bdd2CFjbubZxOfXhVRnzaiB5AwISTRHhMNRQXBO2f+32AXNqH+DxBlJTlEXnmQuUin/XCHCGMQJ7nFH1e/MDYfLiiPgnK17FryMGnPwGWlS4+gkPg3C5tBppCaLlP3vnGIrHoX965HfeJws4W9/nIzet7P6x5BWJ4Ihqz2L8wDm7DvGwDdl0b/eEjj3YNnPlA6YFyoS6oy0OLN4GaEUO/vbQA0fCQw1lxZpPhW+9A9WBscVUTvjb1M4XnwJeelk7ncygSoqmQBvdNG8Ui0C5qW4lw==";
$date = "2024-09-11T09:13:19.114Z";
$jsonBody = '{"beneficiaryAccountNo":"081234567890","amount":{"currency":"Rp","value":20000.00},"originalExternalId":"2024091116114876050","serviceCode":"18","transactionStatusDesc":"Successful","originalPartnerReferenceNo":"2024091116114876050","responseCode":"2004300","originalReferenceNo":"2024091136690000076","referenceNumber":"2024091116114876050","latestTransactionStatus":"00","additionalInfo":{"transFeeRate":0.0100,"totalTransFee":1700.00,"transFeeAmount":1500.00},"responseMessage":"Request has been processed successfully","sourceAccountNo":"010366"}';

$verify = $paylabs->verifySign("/disbursement/callback.php", $jsonBody, $sign, $date);
