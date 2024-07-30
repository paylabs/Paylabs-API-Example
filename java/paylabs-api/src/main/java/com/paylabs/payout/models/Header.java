package com.paylabs.payout.models;

import java.util.HashMap;
import java.util.Map;

public class Header {

  public static Map<String, String> create(String date, String signature, String requestId) {
    Map<String, String> jsonHeader = new HashMap<>();
    jsonHeader.put("X-TIMESTAMP", date);
    jsonHeader.put("X-SIGNATURE", signature);
    jsonHeader.put("X-PARTNER-ID", System.getenv("merchantId"));
    jsonHeader.put("X-EXTERNAL-ID", requestId);
    jsonHeader.put("ORIGIN", "www.paylabs.co.id");
    jsonHeader.put("Content-Type", "application/json;charset=utf-8");

    return jsonHeader;
  }
}
