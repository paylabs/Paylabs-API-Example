package com.paylabs.payout.utils;

import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;

public class Sha256 {

//  public static String hash(String jsonBody) throws Exception {
//    java.security.MessageDigest digest = java.security.MessageDigest.getInstance("SHA-256");
//    byte[] hash = digest.digest(jsonBody.getBytes(StandardCharsets.UTF_8));
//    return Base64.getEncoder().encodeToString(hash);
//  }

  public static String hash(String jsonBody) throws Exception {
    MessageDigest digest = MessageDigest.getInstance("SHA-256");
    byte[] hash = digest.digest(jsonBody.getBytes(StandardCharsets.UTF_8));
    return bytesToHex(hash);
  }

  private static String bytesToHex(byte[] bytes) {
    StringBuilder hexString = new StringBuilder();
    for (byte b : bytes) {
      String hex = Integer.toHexString(0xff & b);
      if (hex.length() == 1) hexString.append('0');
      hexString.append(hex);
    }
    return hexString.toString();
  }
}
