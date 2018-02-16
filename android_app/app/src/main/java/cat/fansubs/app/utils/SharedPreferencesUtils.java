package cat.fansubs.app.utils;

import android.content.Context;
import android.content.SharedPreferences;

import com.google.gson.Gson;

import java.lang.reflect.Type;
import java.util.Set;

import cat.fansubs.app.FansubsApplication;

public class SharedPreferencesUtils {

    public static final String FANSUBS_LIST = "fansubs";
    public static final String FILTER_FANSUB_IDS_LIST = "filter_fansub_ids";
    public static final String UNREAD_PUSHES_LIST = "unread_pushes";
    private static final String DEFAULT_PREFERENCES_NAME = "fansubscat_preferences";
    public static final String NOTIFICATIONS_ENABLED = "notifications_enabled";
    public static final String NOTIFICATION_TEXT_LIST = "notification_texts";
    public static final java.lang.String EASTER_EGG_ENABLED = "easter_egg_enabled";


    public static int getInt(String key) {
        return Integer.parseInt(getInternal(key));
    }

    public static long getLong(String key) {
        return Long.parseLong(getInternal(key));
    }

    public static boolean getBoolean(String key) {
        return Boolean.parseBoolean(getInternal(key));
    }

    public static String getString(String key, String defaultValue) {
        String value = getInternal(key);
        if (value == null) {
            return defaultValue;
        }
        return value;
    }

    @SuppressWarnings("unchecked")
    public static Set<String> getStringSet(String key, Set<String> defaultValue) {
        String value = getInternal(key);
        if (value == null) {
            return defaultValue;
        }
        return (Set<String>) (new Gson().fromJson(value, Set.class));
    }

    public static int getInt(String key, int defaultValue) {
        String value = getInternal(key);
        if (value == null) {
            return defaultValue;
        }
        return Integer.parseInt(value);
    }

    public static long getLong(String key, long defaultValue) {
        String value = getInternal(key);
        if (value == null) {
            return defaultValue;
        }
        return Long.parseLong(value);
    }

    public static boolean getBoolean(String key, boolean defaultValue) {
        String value = getInternal(key);
        if (value == null) {
            return defaultValue;
        }
        return Boolean.parseBoolean(value);
    }

    public String getString(String key) {
        return getInternal(key);
    }

    public static void setString(String key, String value) {
        setInternal(key, value);
    }

    public static void setObject(String key, Object value) {
        setInternal(key, new Gson().toJson(value));
    }

    public static <T> T getObject(String key, T defaultValue, Class<T> clazz) {
        String value = getInternal(key);
        if (value == null) {
            return defaultValue;
        }
        return new Gson().fromJson(value, clazz);
    }

    public static <T> T getObject(String key, T defaultValue, Type type) {
        String value = getInternal(key);
        if (value == null) {
            return defaultValue;
        }
        return new Gson().fromJson(value, type);
    }

    public static void setStringSet(String key, Set<String> value) {
        setInternal(key, new Gson().toJson(value));
    }

    public static void setInt(String key, int value) {
        setInternal(key, String.valueOf(value));
    }

    public static void setLong(String key, long value) {
        setInternal(key, String.valueOf(value));
    }

    public static void setBoolean(String key, boolean value) {
        setInternal(key, String.valueOf(value));
    }

    public static void remove(String key) {
        removeInternal(key);
    }

    private static void removeInternal(String key) {
        FansubsApplication.getInstance().getSharedPreferences(DEFAULT_PREFERENCES_NAME, Context.MODE_PRIVATE).edit().remove(key).apply();
    }

    private static void setInternal(String key, String value) {
        SharedPreferences settings = FansubsApplication.getInstance().getSharedPreferences(DEFAULT_PREFERENCES_NAME, Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = settings.edit();

        if (value != null) {
            editor.putString(key, value);
            editor.apply();
        } else {
            editor.remove(key);
            editor.apply();
        }
    }

    private static String getInternal(String key) {
        Context context = FansubsApplication.getInstance();

        SharedPreferences settings = context.getSharedPreferences(DEFAULT_PREFERENCES_NAME, Context.MODE_PRIVATE);
        return settings.getString(key, null);
    }

    public static void removeAllData() {
        FansubsApplication.getInstance().getSharedPreferences(DEFAULT_PREFERENCES_NAME, Context.MODE_PRIVATE).edit().clear().apply();
    }
}
