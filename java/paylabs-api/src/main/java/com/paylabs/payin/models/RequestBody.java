package com.paylabs.payin.models;
import com.paylabs.payin.utils.JsonSerializable;
import com.paylabs.payin.utils.JsonUtil;
import org.json.JSONObject;
import java.util.List;

public class RequestBody implements JsonSerializable {

  private String merchantId = System.getenv("merchantId");
  private String requestId;
  private String paymentType;
  private String amount;
  private String merchantTradeNo;
  private String payer;
  private String productName;
  private String storeId;
  private String notifyUrl;
  private List<ProductInfo> productInfo;
  private Object paymentParams;

  public RequestBody() {
  }

  public RequestBody(String requestId, String paymentType, String amount, String merchantTradeNo, String payer, String productName) {
    this.requestId = requestId;
    this.paymentType = paymentType;
    this.amount = amount;
    this.merchantTradeNo = merchantTradeNo;
    this.payer = payer;
    this.productName = productName;
  }

  public String getRequestId() {
    return requestId;
  }

  public void setRequestId(String requestId) {
    this.requestId = requestId;
  }

  public String getPaymentType() {
    return paymentType;
  }

  public void setPaymentType(String paymentType) {
    this.paymentType = paymentType;
  }

  public String getAmount() {
    return amount;
  }

  public void setAmount(String amount) {
    this.amount = amount;
  }

  public Object getMerchantTradeNo() {
    return merchantTradeNo;
  }

  public void setMerchantTradeNo(String merchantTradeNo) {
    this.merchantTradeNo = merchantTradeNo;
  }

  public String getPayer() {
    return payer;
  }

  public void setPayer(String payer) {
    this.payer = payer;
  }

  public String getProductName() {
    return productName;
  }

  public void setProductName(String productName) {
    this.productName = productName;
  }

  public String getStoreId() {
    return storeId;
  }

  public void setStoreId(String storeId) {
    this.storeId = storeId;
  }

  public String getNotifyUrl() {
    return notifyUrl;
  }

  public void setNotifyUrl(String notifyUrl) {
    this.notifyUrl = notifyUrl;
  }

  public List<ProductInfo> getProductInfo() {
    return productInfo;
  }

  public void setProductInfo(List<ProductInfo> productInfo) {
    this.productInfo = productInfo;
  }

  public Object getPaymentParams() {
    return paymentParams;
  }

  public void setPaymentParams(Object paymentParams) {
    this.paymentParams = paymentParams;
  }

  @Override
  public JSONObject toJson() {
    return JsonUtil.toJson(this);
  }
}
