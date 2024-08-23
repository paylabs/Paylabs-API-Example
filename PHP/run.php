<?php

include "Paylabs.php";

$paylabs = new Paylabs();

// VALIDATE CALLBACK
$paylabs->setPublicKey("MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAm89foNjHYbx0/gdC1kuCCA8K7ut7iUzS5PNIlcxhkLfnKiuZuonFgF1eBfYJLa7ZWP+W3BoLGcXzFEKMAwycOvDFtG5dqYqghM550oUCrZqIOpD6CziMMf8nHbztrOJDg2/mHURHNfoEx6D5lIRqO9WGkD42I1jeL4qHMnfxKCFGFtRF8/BJqPbvRX4DKVs36DxAacpl901Hy6WG94R1I1g8fBVLIs9QQc5cWu04NepzeP2M32/EbySzl+fzR5z63qzabWSXetkA5l1XFReb3HunHtYrZ5Mrrn32L8mO5q+cj9miu9+cEcPr/xaTEKlwQ/HvRZ2YbRUDQvxciFsKtQIDAQAB");
$json = '{"merchantId":"010418","requestId":"N01041820240708418000000541720426703270","errCode":"0","paymentType":"BCAVA","amount":"94000.00","createTime":"20240708151527","successTime":"20240708151629","merchantTradeNo":"10176-1720426526-58422","platformTradeNo":"2024070841800000054","status":"02","paymentMethodInfo":{"vaCode":"9999900000000006"},"productName":"Fomotoko"}';
$sign = 'Pa08XYz+ktSvTjKwA0CT6D1o34VkmxFE+9ghhl19UEvXkeIU2SjJEHeyJbOEKqUeS30vx/q7+G2D114Fi5cGRe+tbEGkSkK5b016R0DTFrCDWWlRyW5XT8Lu/D59koN868Q1E8oD0ioUAlBJRz7NC9bJzwNBW6IlhiFzst7qTuN9r+U54KZOdjzBzJgbXDD0x/rpllhWLDvJnJDo3p2X9FeSjtCfziKRnJsh0JuucxweMaNL/Mk5Ncn2e6/ew8fVJiE9DA8OGzU1SDOPxj8F0eS8mKCCVqzNkgdGwxMO045cy6exiQHGQq4VsH3ma1fcG3jln7kka1bN+WYqbArKJQ==';
$dateTime = '2024-07-08T15:18:23.270+07:00';
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

$mid = "010366"; //MERCHANT ID
$server = "SIT"; //SIT & PROD
$version = "v2.1";
$privKey = "MIIEowIBAAKCAQEAgb5E5RCXHAtg8WvpGMV+9ouOPLl8FU89VOCc1Di7c7SCEHmFgKjUOitN8iQFrIlNkG2pyKOlD+X3UIY31R2WhOfHD+DcpcrKcVuxXHm+cEg9tFmuGy/xfVWRJf8AkJRgUADnPFHGaL89K0uF0sIXPeOq1ZGaYbMUYqtU1uS/JWY1Vai7TcoblhtDdQ5WMU49CgRcdIOO8lUjkTjjY1ci27duwwt0E9q1JMHjTpa1RHyh66PDfNTj5QlFm1hWhv6vzZC2lmKeyHDLEWoJFYTq+/FS3Oyg+kyjHI3crLPhaIzkl128FTaXvUWuR/6z30vWSXATzwwXP+7SiB3UirgtRQIDAQABAoIBADMQ88cBL6jfJaoAYj/YxC9AIJzwGNG+XZKB0diJ9+YDv0nCuvQ3/0hh8Q0EdqNBa6EUOMZ6+qX2StCL83/TZIItRCGQzOgImIPjpjySwo0pMfhBcBDNXwyI2FePNeayL/JtWJZaTXC/DNF0qViaAlHewUfMRmp79OMdMohegomySa55vAssLFNEi+vHq15wGMt9d1aLoynuVHxPGZ50KtgyjQunA7A0zyg5f8lFm28v7xo8qU8zr5rR+G0+wtsJPjnxoUT/st8TbbOfFYu75w83ss5exzr0MgQmglK6OmxHxdVTgwuO4d9C3I65mssrgGJkNJV0EQhJ/rFYNWxG1GUCgYEA9+dpocHGYuUgcYadSjWGDV1LQsfY5eWVSev3dtZnyRfVpVZO6gmHL2OmUWtunpzFptxbc/6I58vOVJdZikZs2JTPKBauj6Smci7crJMyR3wuICF6ArbStkl//Saw/lFsYmoR0gFD06bIa687OU5ffockjGNMXrUxZ7LhV5F+aWsCgYEAhfr69q70ykr31LeIr21uf0Cu8wdbKKl9lK82vv85/Tdq/8xtTy+59WTDuAyHDFDh67R3ci40+NQSP7vc5SUHSx32LHghoDeMA6TN/1IbCMbDmtaqIR6XzD/FVLp0zszAZkI0LnJKWDVJkNMyZhCEsl6tjXVUVYC3S0y8rJDwAA8CgYEA21VjoDpRzC3tBoSTpZS1hh2E+RDYVo9KBp0/1WTdbo4n+KDkMS087jC8dk0XEj6ioX9VastJVcx8QVunXS/yHa2Lm2x2BaEnot7TX9zcH2M/bC4yRTR8OcvN8azJ7DkeK/Ssz6FO4XQu3xeqzokI9GmdbJhueVzW2Wjq9w9DSQECgYA999+ryTAfgJ0wHdNykELTSK+iaHyZSgtzgbbokFPZ8o/i0EKepYx1G64KqoCsCZz0z/uPLCAEFtJ5+AIrWf0NmUYLO2USHZ788HT26prmbEh8jV0TBHthVP2IOtVb8QfsRCKueN45/iuQeJ6O5oT5myDalLH0+hvNECkUB9V5sQKBgBLY+2ImpQLbTJ3/Rg8eqTjGA7+WN4EKwFPiW3Krbj7urWko9G2OTz9WvJ+LX8YsrlH+PkSSvEpnzWuaBNREnfCTzmguSXkpz0jxQcoCPR7ufZ6x+JhmYnBSBwesWykBUhF8z4PKu5bPN0f4HrhPicJPNmnluJ5dRFBJbixr9Ort"; //MERCHANT PRIVATE KEY

$paylabs->setMid($mid);
$paylabs->setServer($server);
$paylabs->setVersion($version);
$paylabs->setPrivateKey($privKey);

// $h5 = $paylabs->setH5("15000", "081234567890", "Testing", "https://google.com", "Test");
// $qris = $paylabs->setQRIS("15000", "Sepeda");
// $wallet = $paylabs->setEMoney("OVOBALANCE", "10000", "Sepeda");
$va = $paylabs->setVA("MaybankVA", "10000", "Sepeda", "Irfan");
// $cc = $paylabs->setCC("CreditCard", "11000", "Sepeda", "Irfan");
// $credit = $paylabs->setECredit("Indodana", "25000", "Sepeda");
// $otc = $paylabs->setOTC("Indomaret", "25000", "Sepeda", "Irfan");
// $trx = $paylabs->request();

$payment = $paylabs->request();
var_dump($payment);
