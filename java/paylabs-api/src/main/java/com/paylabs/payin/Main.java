package com.paylabs.payin;
import com.paylabs.payin.models.PaymentParams;
import com.paylabs.payin.models.RequestBody;
import com.paylabs.payin.models.ProductInfo;
import com.paylabs.payin.utils.ApiRequest;

import java.util.ArrayList;
import java.util.List;

public class Main {
  public static void main(String[] args) throws Exception {

//    String privateKeyStr = "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCBvkTlEJccC2Dxa+kYxX72i448uXwVTz1U4JzUOLtztIIQeYWAqNQ6K03yJAWsiU2QbanIo6UP5fdQhjfVHZaE58cP4NylyspxW7Fceb5wSD20Wa4bL/F9VZEl/wCQlGBQAOc8UcZovz0rS4XSwhc946rVkZphsxRiq1TW5L8lZjVVqLtNyhuWG0N1DlYxTj0KBFx0g47yVSOROONjVyLbt27DC3QT2rUkweNOlrVEfKHro8N81OPlCUWbWFaG/q/NkLaWYp7IcMsRagkVhOr78VLc7KD6TKMcjdyss+FojOSXXbwVNpe9Ra5H/rPfS9ZJcBPPDBc/7tKIHdSKuC1FAgMBAAECggEAMxDzxwEvqN8lqgBiP9jEL0AgnPAY0b5dkoHR2In35gO/ScK69Df/SGHxDQR2o0FroRQ4xnr6pfZK0Ivzf9Nkgi1EIZDM6AiYg+OmPJLCjSkx+EFwEM1fDIjYV4815rIv8m1YllpNcL8M0XSpWJoCUd7BR8xGanv04x0yiF6CibJJrnm8CywsU0SL68erXnAYy313VoujKe5UfE8ZnnQq2DKNC6cDsDTPKDl/yUWbby/vGjypTzOvmtH4bT7C2wk+OfGhRP+y3xNts58Vi7vnDzeyzl7HOvQyBCaCUro6bEfF1VODC47h30LcjrmayyuAYmQ0lXQRCEn+sVg1bEbUZQKBgQD352mhwcZi5SBxhp1KNYYNXUtCx9jl5ZVJ6/d21mfJF9WlVk7qCYcvY6ZRa26enMWm3Ftz/ojny85Ul1mKRmzYlM8oFq6PpKZyLtyskzJHfC4gIXoCttK2SX/9JrD+UWxiahHSAUPTpshrrzs5Tl9+hySMY0xetTFnsuFXkX5pawKBgQCF+vr2rvTKSvfUt4ivbW5/QK7zB1soqX2Urza+/zn9N2r/zG1PL7n1ZMO4DIcMUOHrtHdyLjT41BI/u9zlJQdLHfYseCGgN4wDpM3/UhsIxsOa1qohHpfMP8VUunTOzMBmQjQuckpYNUmQ0zJmEISyXq2NdVRVgLdLTLyskPAADwKBgQDbVWOgOlHMLe0GhJOllLWGHYT5ENhWj0oGnT/VZN1ujif4oOQxLTzuMLx2TRcSPqKhf1Vqy0lVzHxBW6ddL/IdrYubbHYFoSei3tNf3NwfYz9sLjJFNHw5y83xrMnsOR4r9KzPoU7hdC7fF6rOiQj0aZ1smG55XNbZaOr3D0NJAQKBgD3336vJMB+AnTAd03KQQtNIr6JofJlKC3OBtuiQU9nyj+LQQp6ljHUbrgqqgKwJnPTP+48sIAQW0nn4AitZ/Q2ZRgs7ZRIdnvzwdPbqmuZsSHyNXRMEe2FU/Yg61VvxB+xEIq543jn+K5B4no7mhPmbINqUsfT6G80QKRQH1XmxAoGAEtj7YialAttMnf9GDx6pOMYDv5Y3gQrAU+JbcqtuPu6taSj0bY5PP1a8n4tfxiyuUf4+RJK8SmfNa5oE1ESd8JPOaC5JeSnPSPFBygI9Hu59nrH4mGZicFIHB6xbKQFSEXzPg8q7ls83R/geuE+Jwk82aeW4nl1EUEluLGv06u0=";
//    String rand = String.valueOf((int) (Math.random() * Integer.MAX_VALUE));
//    String merchantId = "010366";
//
//    RequestBody body = new RequestBody(rand, merchantId, "DANABALANCE", "25000", rand, null, "Pulsa");
//
//    body.setNotifyUrl("https://google.com");

//    USE PRODUCT INFO
//    List<ProductInfo> productInfos = new ArrayList<>();
//    ProductInfo product1 = new ProductInfo("1", "Pulsa", "25000", "pulsa", "https://google.com", "1");
//    productInfos.add(product1);
//    body.setProductInfo(productInfos);

//    USE PAYMENT PARAMS
//    PaymentParams params  = new PaymentParams();
//    params.setRedirectUrl("https://google.com");
//    body.setPaymentParams(params.toJson());
//    System.out.println(body.toJson());

//    String jsonBody = body.toJson().toString();
//    System.out.println(jsonBody);
//    String createPayment = ApiRequest.create(body.toJson(), privateKeyStr, "https://sit-pay.paylabs.co.id", "/payment/v2.1/ewallet/create");
//    System.out.println(createPayment);
//
    String rand = String.valueOf((int) (Math.random() * Integer.MAX_VALUE));
    RequestBody body = new RequestBody(rand, "DANABALANCE", "25000", rand, null, "Pulsa");
    body.setNotifyUrl("https://google.com");

//    USE PRODUCT INFO
    List<ProductInfo> productInfos = new ArrayList<>();
    ProductInfo product1 = new ProductInfo("1", "Pulsa", "25000", "pulsa", "https://google.com", "1");
    productInfos.add(product1);
    body.setProductInfo(productInfos);

//    USE PAYMENT PARAMS
    PaymentParams params  = new PaymentParams();
    params.setRedirectUrl("https://google.com");
    body.setPaymentParams(params.toJson());
    System.out.println(body.toJson());

    String jsonBody = body.toJson().toString();
    System.out.println(jsonBody);

    String createPayment = ApiRequest.create(body.toJson(), "/payment/v2.1/ewallet/create");
    System.out.println(createPayment);
  }
}