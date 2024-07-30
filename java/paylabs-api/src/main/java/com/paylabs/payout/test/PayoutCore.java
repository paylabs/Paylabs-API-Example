package com.paylabs.payout.test;

import com.paylabs.payout.utils.ApiRequest;
import com.paylabs.payout.utils.RandomStr;
import org.json.JSONObject;

public class PayoutCore {

  public static JSONObject accountInquiry(String accountNo, String bankCode, String type, String amount) {
    String randomStr = RandomStr.random();

    JSONObject additional = new JSONObject();
    additional.put("remitType", type);
    additional.put("amount", amount);

    JSONObject body = new JSONObject();
    body.put("partnerReferenceNo", randomStr);
    body.put("beneficiaryAccountNo", accountNo);
    body.put("beneficiaryBankCode", bankCode);
    body.put("additionalInfo", additional);
    System.out.println(body);

    String request = ApiRequest.create(body, "/api-pay/snap/"+System.getenv("version")+"/account-inquiry-external");
    JSONObject resp = new JSONObject(request);

    return resp;
  }

  public static JSONObject transferInterbank(String customerReference, String beneficiaryAccountNo, String beneficiaryAccountName, String beneficiaryBankCode, String type, String amount){
    String randomStr = RandomStr.random();

    JSONObject body = new JSONObject();
    body.put("partnerReferenceNo", randomStr);
    body.put("customerReference", customerReference);
    body.put("beneficiaryAccountNo", beneficiaryAccountNo);
    body.put("beneficiaryAccountName", beneficiaryAccountName);
    body.put("beneficiaryBankCode", beneficiaryBankCode);

    JSONObject amounts = new JSONObject();
    amounts.put("value", amount);
    amounts.put("currency", "Rp");
    body.put("amount", amounts);

    JSONObject additionalInfo = new JSONObject();
    additionalInfo.put("remitType", type);
    body.put("additionalInfo", additionalInfo);

    String request = ApiRequest.create(body, "/api-pay/snap/"+System.getenv("version")+"/transfer-interbank");
    JSONObject resp = new JSONObject(request);

    return resp;
  }

  public static JSONObject transferStatus(String referenceNo){
    String randomStr = RandomStr.random();

    JSONObject body = new JSONObject();
    body.put("originalPartnerReferenceNo", referenceNo);
    body.put("originalExternalId", randomStr);

    String request = ApiRequest.status(body, "/api-pay/snap/"+System.getenv("version")+"/transfer/status");
    JSONObject resp = new JSONObject(request);

    return resp;
  }

  public static JSONObject getBalance(){
    String randomStr = RandomStr.random();

    JSONObject body = new JSONObject();
    body.put("partnerReferenceNo", randomStr);
    body.put("accountNo", System.getenv("merchantId"));

    String request = ApiRequest.create(body, "/api-pay/snap/"+System.getenv("version")+"/balance-inquiry");
    JSONObject resp = new JSONObject(request);

    return resp;
  }
}
