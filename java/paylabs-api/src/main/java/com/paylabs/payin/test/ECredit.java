package com.paylabs.payin.test;

import com.paylabs.payin.models.PaymentParams;
import com.paylabs.payin.models.ProductInfo;
import com.paylabs.payin.models.RequestBody;
import com.paylabs.payin.utils.ApiRequest;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

public class ECredit {
  public static String create(){
    String randomString = String.valueOf( (int) (Math.random() * Integer.MAX_VALUE) );
    RequestBody body = new RequestBody(
        randomString,
        "Indodana",
        "50000",
        randomString,
        "Jordan",
        "Hat"
    );

    PaymentParams params = new PaymentParams();
    params.setRedirectUrl("https://google.com");
    body.setPaymentParams(params.toJson());

    List<ProductInfo> productInfos = new ArrayList<>();
    ProductInfo productInfo1 = new ProductInfo("1", "Hat", "50000", "accessories", "https://google.com", "1");
    productInfos.add(productInfo1);

    body.setProductInfo(productInfos);
    JSONObject jsonBody = body.toJson();

    String create = ApiRequest.create(jsonBody, "/payment/"+System.getenv("version")+"/dd/create");
    return create;
  }
}
