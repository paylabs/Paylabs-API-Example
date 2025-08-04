package com.paylabs.verifyCallback;

import java.security.*;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.Base64;

public class Paylabs {
  private String server = "SIT";
  private String mid;
  private String version;
  private String privateKey;
  private String publicKey;
  private String date;

  public Paylabs() {
    date = LocalDateTime.now().format(DateTimeFormatter.ofPattern("yyyy-MM-dd'T'HH:mm:ss")) + "+07:00";
  }

  public void setMid(String mid) {
    this.mid = mid;
  }

  public void setVersion(String version) {
    this.version = version;
  }

  public void setServer(String server) {
    this.server = server;
  }

  public void setPrivateKey(String privateKey) {
    this.privateKey = privateKey;
  }

  public void setPublicKey(String publicKey) {
    this.publicKey = publicKey;
  }

  public boolean verifySign(String path, String dataToSign, String sign, String dateTime) {
    try {
      Signature signature = Signature.getInstance("SHA256withRSA");
      PublicKey pubKey = KeyFactory.getInstance("RSA").generatePublic(
          new java.security.spec.X509EncodedKeySpec(Base64.getDecoder().decode(publicKey))
      );
      signature.initVerify(pubKey);

      String toVerify = "POST:" + path + ":" + sha256(dataToSign) + ":" + dateTime;
      signature.update(toVerify.getBytes());

      return signature.verify(Base64.getDecoder().decode(sign));
    } catch (Exception e) {
      e.printStackTrace();
      return false;
    }
  }

  private String sha256(String data) {
    try {
      MessageDigest digest = MessageDigest.getInstance("SHA-256");
      byte[] hash = digest.digest(data.getBytes());
      StringBuilder hexString = new StringBuilder();
      for (byte b : hash) {
        String hex = Integer.toHexString(0xff & b);
        if (hex.length() == 1) hexString.append('0');
        hexString.append(hex);
      }
      return hexString.toString();
    } catch (Exception e) {
      throw new RuntimeException(e);
    }
  }
}
