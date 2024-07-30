package com.paylabs.payout.utils;

import java.nio.charset.StandardCharsets;
import java.security.KeyFactory;
import java.security.PrivateKey;
import java.security.Signature;
import java.security.spec.PKCS8EncodedKeySpec;
import java.util.Base64;

public class Sign {

  public static String sign(String data) throws Exception {
    String privateKeyPem = System.getenv("privateKey").replace("-----BEGIN RSA PRIVATE KEY-----", "")
        .replace("-----END RSA PRIVATE KEY-----", "")
        .replaceAll("\\s", "");

    byte[] keyBytes = Base64.getDecoder().decode(privateKeyPem);

    KeyFactory keyFactory = KeyFactory.getInstance("RSA");
    PKCS8EncodedKeySpec keySpec = new PKCS8EncodedKeySpec(keyBytes);
    PrivateKey privateKey = keyFactory.generatePrivate(keySpec);

    Signature privateSignature = Signature.getInstance("SHA256withRSA");
    privateSignature.initSign(privateKey);
    privateSignature.update(data.getBytes(StandardCharsets.UTF_8));

    byte[] signature = privateSignature.sign();
    return Base64.getEncoder().encodeToString(signature);
  }

}
