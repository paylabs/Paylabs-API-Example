package com.paylabs.payout.models;

import com.paylabs.payout.utils.JsonSerializable;
import com.paylabs.payout.utils.JsonUtil;
import org.json.JSONObject;

import java.util.List;

public class RequestBody implements JsonSerializable {

  private String partnerReferenceNo;
  private String beneficiaryAccountNo;
  private String beneficiaryBankCode;
  private JSONObject additionalInfo;

  public RequestBody() {
  }

  public String getPartnerReferenceNo() {
    return partnerReferenceNo;
  }

  public void setPartnerReferenceNo(String partnerReferenceNo) {
    this.partnerReferenceNo = partnerReferenceNo;
  }

  public String getBeneficiaryAccountNo() {
    return beneficiaryAccountNo;
  }

  public void setBeneficiaryAccountNo(String beneficiaryAccountNo) {
    this.beneficiaryAccountNo = beneficiaryAccountNo;
  }

  public String getBeneficiaryBankCode() {
    return beneficiaryBankCode;
  }

  public void setBeneficiaryBankCode(String beneficiaryBankCode) {
    this.beneficiaryBankCode = beneficiaryBankCode;
  }

  public JSONObject getAdditionalInfo() {
    return additionalInfo;
  }

  public void setAdditionalInfo(JSONObject additionalInfo) {
    this.additionalInfo = additionalInfo;
  }

  @Override
  public JSONObject toJson() {
    return JsonUtil.toJson(this);
  }
}
