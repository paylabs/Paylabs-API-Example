package com.paylabs.payin.models;

import com.paylabs.payin.utils.JsonSerializable;
import com.paylabs.payin.utils.JsonUtil;
import org.json.JSONObject;

public class ProductInfo implements JsonSerializable {
  private String id;
  private String name;
  private String price;
  private String type;
  private String url;
  private String quantity;

  public ProductInfo(String id, String name, String price, String type, String url, String quantity) {
    this.id = id;
    this.name = name;
    this.price = price;
    this.type = type;
    this.url = url;
    this.quantity = quantity;
  }

  @Override
  public JSONObject toJson() {
    return JsonUtil.toJson(this);
  }
}
