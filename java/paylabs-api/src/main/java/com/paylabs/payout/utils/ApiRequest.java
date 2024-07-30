package com.paylabs.payout.utils;

import com.paylabs.payout.models.Header;
import org.apache.http.client.methods.CloseableHttpResponse;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.util.EntityUtils;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Map;

public class ApiRequest {

  private static String date = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.S'Z'").format(new Date());

  public static String create(JSONObject jsonBody, String path){
    try (CloseableHttpClient httpClient = HttpClients.createDefault()) {
      String jsonBodyStr = jsonBody.toString();
      System.out.println("JSON Body : " + jsonBodyStr);

      String signature = CreateSign.create(path, jsonBodyStr, date);
      System.out.println("signature : " + signature);

      Map<String, String> header = Header.create(date, signature, jsonBody.getString("partnerReferenceNo"));
      System.out.println("Header : "+header);

      HttpPost post = new HttpPost(System.getenv("url")+path);
      header.forEach(post::setHeader);

      StringEntity entity = new StringEntity(jsonBodyStr);
      post.setEntity(entity);

      try (CloseableHttpResponse response = httpClient.execute(post)) {
        int responseCode = response.getStatusLine().getStatusCode();
        System.out.println("Response Code : " + responseCode);

        String responseBody = EntityUtils.toString(response.getEntity());
        System.out.println("Response Body : " + responseBody);
        return responseBody;
      }

    } catch (Exception e) {
      e.printStackTrace();
    }
    return null;
  }

  public static String status(JSONObject jsonBody, String path){
    try (CloseableHttpClient httpClient = HttpClients.createDefault()) {
      String jsonBodyStr = jsonBody.toString();
      System.out.println("JSON Body : " + jsonBodyStr);

      String signature = CreateSign.create(path, jsonBodyStr, date);
      System.out.println("signature : " + signature);

      Map<String, String> header = Header.create(date, signature, jsonBody.getString("originalExternalId"));
      System.out.println("Header : "+header);

      HttpPost post = new HttpPost(System.getenv("url")+path);
      header.forEach(post::setHeader);

      StringEntity entity = new StringEntity(jsonBodyStr);
      post.setEntity(entity);

      try (CloseableHttpResponse response = httpClient.execute(post)) {
        int responseCode = response.getStatusLine().getStatusCode();
        System.out.println("Response Code : " + responseCode);

        String responseBody = EntityUtils.toString(response.getEntity());
        System.out.println("Response Body : " + responseBody);
        return responseBody;
      }

    } catch (Exception e) {
      e.printStackTrace();
    }
    return null;
  }
}
