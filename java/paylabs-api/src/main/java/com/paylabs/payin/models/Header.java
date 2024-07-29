package com.paylabs.payin.models;

import java.util.HashMap;
import java.util.Map;

public class Header {

  public static Map<String, String> create(String date, String signature, String merchantId, String requestId) {
    Map<String, String> jsonHeader = new HashMap<>();
    jsonHeader.put("X-TIMESTAMP", date);
    jsonHeader.put("X-SIGNATURE", signature);
    jsonHeader.put("X-PARTNER-ID", merchantId);
    jsonHeader.put("X-REQUEST-ID", requestId);
    jsonHeader.put("Content-Type", "application/json;charset=utf-8");

    return jsonHeader;
  }
}
