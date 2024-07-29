package com.paylabs.payin;

import java.security.KeyFactory;
import java.security.MessageDigest;
import java.security.PrivateKey;
import java.security.Signature;
import java.security.spec.PKCS8EncodedKeySpec;
import java.util.Base64;
import org.json.JSONObject;

import java.nio.charset.StandardCharsets;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;
import org.apache.http.client.methods.CloseableHttpResponse;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.util.EntityUtils;

public class Test {

  public static void main(String[] args) throws Exception {
    // Set timezone
    // TimeZone.setDefault(TimeZone.getTimeZone("Asia/Jakarta")); // Uncomment if needed

    String privateKeyStr = "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCBvkTlEJccC2Dxa+kYxX72i448uXwVTz1U4JzUOLtztIIQeYWAqNQ6K03yJAWsiU2QbanIo6UP5fdQhjfVHZaE58cP4NylyspxW7Fceb5wSD20Wa4bL/F9VZEl/wCQlGBQAOc8UcZovz0rS4XSwhc946rVkZphsxRiq1TW5L8lZjVVqLtNyhuWG0N1DlYxTj0KBFx0g47yVSOROONjVyLbt27DC3QT2rUkweNOlrVEfKHro8N81OPlCUWbWFaG/q/NkLaWYp7IcMsRagkVhOr78VLc7KD6TKMcjdyss+FojOSXXbwVNpe9Ra5H/rPfS9ZJcBPPDBc/7tKIHdSKuC1FAgMBAAECggEAMxDzxwEvqN8lqgBiP9jEL0AgnPAY0b5dkoHR2In35gO/ScK69Df/SGHxDQR2o0FroRQ4xnr6pfZK0Ivzf9Nkgi1EIZDM6AiYg+OmPJLCjSkx+EFwEM1fDIjYV4815rIv8m1YllpNcL8M0XSpWJoCUd7BR8xGanv04x0yiF6CibJJrnm8CywsU0SL68erXnAYy313VoujKe5UfE8ZnnQq2DKNC6cDsDTPKDl/yUWbby/vGjypTzOvmtH4bT7C2wk+OfGhRP+y3xNts58Vi7vnDzeyzl7HOvQyBCaCUro6bEfF1VODC47h30LcjrmayyuAYmQ0lXQRCEn+sVg1bEbUZQKBgQD352mhwcZi5SBxhp1KNYYNXUtCx9jl5ZVJ6/d21mfJF9WlVk7qCYcvY6ZRa26enMWm3Ftz/ojny85Ul1mKRmzYlM8oFq6PpKZyLtyskzJHfC4gIXoCttK2SX/9JrD+UWxiahHSAUPTpshrrzs5Tl9+hySMY0xetTFnsuFXkX5pawKBgQCF+vr2rvTKSvfUt4ivbW5/QK7zB1soqX2Urza+/zn9N2r/zG1PL7n1ZMO4DIcMUOHrtHdyLjT41BI/u9zlJQdLHfYseCGgN4wDpM3/UhsIxsOa1qohHpfMP8VUunTOzMBmQjQuckpYNUmQ0zJmEISyXq2NdVRVgLdLTLyskPAADwKBgQDbVWOgOlHMLe0GhJOllLWGHYT5ENhWj0oGnT/VZN1ujif4oOQxLTzuMLx2TRcSPqKhf1Vqy0lVzHxBW6ddL/IdrYubbHYFoSei3tNf3NwfYz9sLjJFNHw5y83xrMnsOR4r9KzPoU7hdC7fF6rOiQj0aZ1smG55XNbZaOr3D0NJAQKBgD3336vJMB+AnTAd03KQQtNIr6JofJlKC3OBtuiQU9nyj+LQQp6ljHUbrgqqgKwJnPTP+48sIAQW0nn4AitZ/Q2ZRgs7ZRIdnvzwdPbqmuZsSHyNXRMEe2FU/Yg61VvxB+xEIq543jn+K5B4no7mhPmbINqUsfT6G80QKRQH1XmxAoGAEtj7YialAttMnf9GDx6pOMYDv5Y3gQrAU+JbcqtuPu6taSj0bY5PP1a8n4tfxiyuUf4+RJK8SmfNa5oE1ESd8JPOaC5JeSnPSPFBygI9Hu59nrH4mGZicFIHB6xbKQFSEXzPg8q7ls83R/geuE+Jwk82aeW4nl1EUEluLGv06u0=";

    int rand = (int) (Math.random() * Integer.MAX_VALUE);
    String date = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.S'+07:00'").format(new Date());
//    String date = "2024-07-10T10:19:07.517Z";
    System.out.println(date);
    String merchantId = "010366";
    int idRequest = rand;
    String merchantTradeNo = "TEST" + rand + rand;

    JSONObject body = new JSONObject();
    body.put("requestId", String.valueOf(idRequest));
    body.put("merchantId", merchantId);
    body.put("paymentType", "MaybankVA");
    body.put("amount", "20000");
    body.put("merchantTradeNo", merchantTradeNo);
    body.put("payer", "Irfan");
    body.put("productName", "Pulsa");

    String jsonBody = body.toString();
    System.out.println(jsonBody);
//    String shaJson = sha256(jsonBody).toLowerCase();
    String shaJson = hash(jsonBody).toLowerCase();
    System.out.println(shaJson);
    String signatureBefore = "POST:/payment/v2.1/va/create:" + shaJson + ":" + date;
    System.out.println(signatureBefore);

    String signature = sign(privateKeyStr, signatureBefore);
    System.out.println(signature);

    try (CloseableHttpClient httpClient = HttpClients.createDefault()) {

      Map<String, String> jsonHeader = new HashMap<>();
      jsonHeader.put("X-TIMESTAMP", date);
      jsonHeader.put("X-SIGNATURE", signature);
      jsonHeader.put("X-PARTNER-ID", merchantId);
      jsonHeader.put("X-REQUEST-ID", String.valueOf(idRequest));
      jsonHeader.put("Content-Type", "application/json;charset=utf-8");
      jsonHeader.put("ORIGIN", "www.paylabs.co.id");

      HttpPost post = new HttpPost("https://sit-pay.paylabs.co.id/payment/v2.1/va/create");
      jsonHeader.forEach(post::setHeader);

      StringEntity entity = new StringEntity(jsonBody);
      post.setEntity(entity);

      try (CloseableHttpResponse response = httpClient.execute(post)) {
        int responseCode = response.getStatusLine().getStatusCode();
        System.out.println("Response Code : " + responseCode);

        String responseBody = EntityUtils.toString(response.getEntity());
        System.out.println("Response Body : " + responseBody);
      }

    } catch (Exception e) {
      e.printStackTrace();
    }

  }

  public static String sha256(String data) throws Exception {
    java.security.MessageDigest digest = java.security.MessageDigest.getInstance("SHA-256");
    byte[] hash = digest.digest(data.getBytes(StandardCharsets.UTF_8));
    return Base64.getEncoder().encodeToString(hash);
  }

  public static String sign(String privateKeyPem, String data) throws Exception {
    privateKeyPem = privateKeyPem.replace("-----BEGIN RSA PRIVATE KEY-----", "")
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

