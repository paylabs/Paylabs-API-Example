package com.paylabs.payout.utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.lang.reflect.Field;
import java.util.List;

public class JsonUtil {

  public static JSONObject toJson(Object obj) {
    JSONObject jsonObject = new JSONObject();
    Field[] fields = obj.getClass().getDeclaredFields();

    for (Field field : fields) {
      field.setAccessible(true);
      try {
        Object value = field.get(obj);
        if (value != null) {
          if (value instanceof List<?>) {
            JSONArray jsonArray = new JSONArray();
            for (Object item : (List<?>) value) {
              if (item instanceof JsonSerializable) {
                jsonArray.put(((JsonSerializable) item).toJson());
              } else {
                jsonArray.put(item);
              }
            }
            jsonObject.put(field.getName(), jsonArray);
          } else {
            jsonObject.put(field.getName(), value);
          }
        }
      } catch (IllegalAccessException e) {
        e.printStackTrace();
      }
    }

    return jsonObject;
  }
}
