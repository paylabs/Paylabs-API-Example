package com.paylabs.payout.test;


import org.json.JSONObject;

public class Main {
  public static void main(String[] args) {
    JSONObject inquiry = PayoutCore.accountInquiry("406040604060", "014", "bank", "20000");
    JSONObject transferInterbank = PayoutCore.transferInterbank(inquiry.getString("referenceNo"), inquiry.getString("beneficiaryAccountNo"), inquiry.getString("beneficiaryAccountName"), inquiry.getString("beneficiaryBankCode"), "bank", "20000");
    JSONObject transferStatus = PayoutCore.transferStatus(transferInterbank.getString("partnerReferenceNo"));
    JSONObject balance = PayoutCore.getBalance();
  }
}
