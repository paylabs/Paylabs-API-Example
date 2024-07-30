package com.paylabs.payout.utils;

public class RandomStr {
  public static String random(){
    return String.valueOf((int) (Math.random() * Integer.MAX_VALUE));
  }
}
