package com.paylabs.payin.models;

import com.paylabs.payin.utils.JsonSerializable;
import com.paylabs.payin.utils.JsonUtil;
import org.json.JSONObject;

public class PaymentParams implements JsonSerializable {

  private String redirectUrl;
  private String phoneNumber;

  public PaymentParams() {
  }

  public String getRedirectUrl() {
    return redirectUrl;
  }

  public void setRedirectUrl(String redirectUrl) {
    this.redirectUrl = redirectUrl;
  }

  public String getPhoneNumber() {
    return phoneNumber;
  }

  public void setPhoneNumber(String phoneNumber) {
    this.phoneNumber = phoneNumber;
  }

  @Override
  public JSONObject toJson() {
    return JsonUtil.toJson(this);
  }
}
