package com.paylabs.verifyCallback;

public class Run {
  public static void main(String[] args) {
    String mid = "010366"; // MERCHANT ID
    String server = "SIT"; // SIT & PROD
    String version = "v2.1";
    String privKey = "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCzwd34MKHBh+IoKStgc8b4EFjq/Sdk0GasntyIZ+lXheju+HEdw/7bUr5ktEzmGHLlI2Ic2Fol1Cd6oKdjfW6UBCj/GXsi26pOxwWGNCiVxKVJkakESDWog9pALPea2vVnpOL+jw5OXXmTbgUE9kvfPvRQXerYnnEEqNaQcp18dpMZ20KTo6HK4vvKszilplMs572aGFTqQF97SntNnPcR3PbnIeovIS3kQgpzu/mxJwQ9S2z0LL3kYNDTX8OKwC2qX31+RfHaNZhA4ojzdJY8kh/hlyP/Jc1PUZxEA84ua1OSThuuLOs7XXjFV4RfOmMcOw1VZb1ensTrOuIQ7ahnAgMBAAECggEANX2lPevK1rpG1Eib/dG2Da9fpl260UcqpoeHfJffWgohLKuFjaP7POy8d18n2vi/h+wlkAvZl5QwQ093QCXdN/Kc+HxupgrT/SlAPZVb1mV0AhZ0Qy/xt7m0wpAfBlfN2w+39KRBT5SFTxfYKofYQBCNxzSLY/aSDx7m6okQ0C5ARgktiPyHFU5RBWH5AmSUghU2t5umiO8T8LnvoYLBbDS39N5L6/Up60SScDmL5sXG3rJ+tjlwe+tgbkPJWVcpgxlzr8qJmXhYOjne1qElVjbul26KeyQ1+eXaL2wNEgSATYpFWlBp5an0nGhESC8WrsNijpvFPwwU4pQtIHLDIQKBgQDwxLBpYzzhFBzJfsnKWf6HY/40JBUHhqiBO9JARaTrZhnDXTMF7J+PIkPZhV/I21ZBGAo02JNWD6Itj8J9Yuuk6akXast3It+au4+3n4ZBi0gjTfuYxmEGk+pgjQ2EdWESp07weiuedOHK3qDE+YcmJQqRIhM7wCTq3tLxYIC7LwKBgQC/IRZS2L1IvJWLRjpdrBCCohznrL9AM7YnSdCzcBzDJUNKhCduiOeQoKunPwMBVoqrZ+vZjwjsDZyv5ZjQ5EXOy1UlB4D41sMoQR10xuVuZz//5/In7zJYOo1g+m5W+uPXFrOgBkYv17vW+8i47tTiUD252COKUxWJhQclh5o4SQKBgQC+PEq/GmAR31hXhMxgC/dmdoV2IURUa/tcES4xBlYmgMe+azdTaQExkPtXNWDXTSa7zAjUdIMFrtiPc1VSyQQzi0PFcuZMvcs7cRM/HNZxl2FqM8Wlkqk7D0hS/WA+1pfaq0NeEsnAK830Ay+4MwisBPxybCASR+FjiqfVfFB6lQKBgHZguOHhKgN2Ai179sRzwNoYLEz1upK6NJTK7D9nLflUCASMK5wToVD7orV4mpTmlAar2/Eo3T7PA4ASdX7UE8SMPZUsu53j7oXtJqBgXFPwETyf3WUMiqMIyO4KzfKDfECI6AIyQKB7PvVdhym7ixEM395OV1ziLtG5Gf8NqOA5AoGAUfEt0sIld/SB6VrDd6FGm2Ho7LcaAx3UJuFBepL/LfycvpDCz0p/4lomsjpoftBWA7I6X3h9LWA0hplMKTX816tVmKCs/x+WWvT7N8jTIYcFEobTi+2hVKxncrxQrwQCQVXBcJEYarrqRUGI8dDlYcg9Y+UfU8eA2ML4gltrvTE="; // MERCHANT PRIVATE KEY

    // SET DATA
    Paylabs paylabs = new Paylabs();
    paylabs.setMid(mid);
    paylabs.setServer(server);
    paylabs.setVersion(version);
    paylabs.setPublicKey("MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsjufK6HhvscZEToMNygH2T95HfFrZ/14uPD4P/MnNhGaB6cOT5pwzbB+aNivZ5alSPXvlAWblzF1kcJGTSoh7CL+cyjfiFkFynD95qqJZxU3Qx1rfYzz5wuyQ7ytWDF8QG3gRQBpDl248FPXprM6VN//kriWeq7hGXw27h3TyAtcfd4kn3FHLxts6ZW3eyIbEjvIh6f+jtPePUbUpAMFG11/LKLI8xcYs5wgJc2qqwC9FXZB24sz/pvGJFAUD3HCBMA//FrU+lecGfk2n2uLFf/K4V5psuO8fEgoX7ZwE5blKwuRAVBBJZNiRPyIKtKK/vJRvu0rs1PQnbDsBrvwswIDAQAB"); // PAYLABS PUBLIC KEY

    String httpSign = "TFYDv9KtolsIuDHBa5Oqer6QbokXWMXrdkx0+Pr3lS2bdJ0Ax4NPXlpImNiS1IS2wImT/fVLE6mxtN2Ps0U7qQGMGwyoMqEenrCgusO1DaZqRfSUZ6MFdk0FE8Dm1Br7dqsYfJmxSH3iGeE5rea64naNQz//T9UwhHaW1CPD4LpwJNrKekHoE/TODNDynZLphu+G2mq0cWYDL9NYQGRuYxcmcsEUWmWfTopgHKB3lwN9aoPFVun7X3dSFvb+5mdaidpKBN4CQ/lhCahHRMk27L9FV0yZ59MWlBjRtLmvmVejOIfvlO9FLLKl+eBO72J9VfpX+9YVxGdwcrLA7QncFg==";
    String dateTime = "2025-08-04T15:50:24+07:00";

    String rawdata = "{\"partnerServiceId\":\"010366\",\"customerNo\":\"00000000000000000000\",\"virtualAccountNo\":\"1467575170245378\",\"virtualAccountName\":\"Vehicle Tax KT1020MT\",\"trxId\":\"2025080415430212594321917\",\"paymentRequestId\":\"2025080436600000174\",\"channelCode\":445,\"paidAmount\":{\"value\":\"2631125.00\",\"currency\":\"IDR\"},\"totalAmount\":{\"value\":\"2631125.00\",\"currency\":\"IDR\"},\"trxDateTime\":\"2025-08-04T15:43:28+07:00\",\"referenceNo\":\"2025080436600000174\",\"additionalInfo\":{\"paymentType\":\"BCAVA\",\"transFeeRate\":\"0.000000\",\"vatFee\":\"0\",\"totalTransFee\":\"2000.00\",\"transFeeAmount\":\"2000.00\"}}";


    // HANDLE CALLBACK SNAP
    boolean validasi = paylabs.verifySign("/transfer-va/payment", rawdata, httpSign, dateTime);
    System.out.println("Validasi: " + validasi);
  }
}
