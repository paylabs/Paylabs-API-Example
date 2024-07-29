package com.paylabs.payin.utils;

public class CreateSign {

  public static String create (String path, String jsonBody, String date) throws Exception {
    String shaJson = Sha256.hash(jsonBody);
    String signatureBefore = "POST:"+path+":" + shaJson + ":" + date;
    System.out.println("String : "+signatureBefore);

    return Sign.sign(signatureBefore);
  }
}
