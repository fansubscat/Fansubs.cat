package cat.fansubs.app.utils;

import com.google.gson.FieldNamingPolicy;
import com.google.gson.GsonBuilder;
import com.google.gson.reflect.TypeToken;

import java.io.InputStream;
import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.ThreadLocalRandom;

import cat.fansubs.app.FansubsApplication;
import cat.fansubs.app.beans.Fansub;
import cat.fansubs.app.beans.UnreadPush;

public class DataUtils {
    private static List<Fansub> fansubs = null;

    public static List<Fansub> retrieveFansubs() {
        if (fansubs == null) {
            Type typeOfT = new TypeToken<List<Fansub>>() {
            }.getType();
            fansubs = SharedPreferencesUtils.getObject(SharedPreferencesUtils.FANSUBS_LIST, null, typeOfT);

            if (fansubs == null) {
                fansubs = loadFansubsFromAssets();
                SharedPreferencesUtils.setObject(SharedPreferencesUtils.FANSUBS_LIST, fansubs);
            }
        }

        return fansubs;
    }

    public static List<String> retrieveFilterFansubIds() {
        Type typeOfT = new TypeToken<List<String>>() {
        }.getType();
        return SharedPreferencesUtils.getObject(SharedPreferencesUtils.FILTER_FANSUB_IDS_LIST, new ArrayList<String>(), typeOfT);
    }

    public static List<String> retrieveNotificationTextFilters() {
        Type typeOfT = new TypeToken<List<String>>() {
        }.getType();
        return SharedPreferencesUtils.getObject(SharedPreferencesUtils.NOTIFICATION_TEXT_LIST, new ArrayList<String>(), typeOfT);
    }

    public static void storeNotificationTextFilters(List<String> notificationTextFilters) {
        SharedPreferencesUtils.setObject(SharedPreferencesUtils.NOTIFICATION_TEXT_LIST, notificationTextFilters);
    }

    public static void storeFilterFansubIds(List<String> filterFansubIds) {
        SharedPreferencesUtils.setObject(SharedPreferencesUtils.FILTER_FANSUB_IDS_LIST, filterFansubIds);
    }

    public static void storeFansubs(List<Fansub> fansubs) {
        DataUtils.fansubs = fansubs;
        SharedPreferencesUtils.setObject(SharedPreferencesUtils.FANSUBS_LIST, fansubs);
    }

    public static Fansub getFansubById(String fansubId) {
        List<Fansub> fansubs = retrieveFansubs();
        for (Fansub fansub : fansubs) {
            if (fansub.getId().equals(fansubId)) {
                return fansub;
            }
        }
        return new Fansub();
    }

    public static Fansub getFansubByHashcodeId(int hashcode) {
        List<Fansub> fansubs = retrieveFansubs();
        for (Fansub fansub : fansubs) {
            if (fansub.getId().hashCode() == hashcode) {
                return fansub;
            }
        }
        return new Fansub();
    }

    public static List<UnreadPush> retrieveUnreadPushes() {
        Type typeOfT = new TypeToken<List<UnreadPush>>() {
        }.getType();
        return SharedPreferencesUtils.getObject(SharedPreferencesUtils.UNREAD_PUSHES_LIST, new ArrayList<UnreadPush>(), typeOfT);
    }

    public static void storeUnreadPushes(List<UnreadPush> unreadPushes) {
        SharedPreferencesUtils.setObject(SharedPreferencesUtils.UNREAD_PUSHES_LIST, unreadPushes);
    }

    public static String getRandomImageUrl() {
        if (SharedPreferencesUtils.getBoolean(SharedPreferencesUtils.EASTER_EGG_ENABLED)) {
            return "https://www.fansubs.cat/style/images/mobileheaderextra.jpg";
        } else {
            return "https://www.fansubs.cat/style/images/mobileheader" + ThreadLocalRandom.current().nextInt(1, 9) + ".jpg";
        }
    }

    private static List<Fansub> loadFansubsFromAssets() {
        String json;
        try {
            InputStream is = FansubsApplication.getInstance().getAssets().open("fansubs.json");
            int size = is.available();
            byte[] buffer = new byte[size];
            //noinspection ResultOfMethodCallIgnored
            is.read(buffer);
            is.close();
            json = new String(buffer, "UTF-8");
        } catch (Exception ex) {
            return new ArrayList<>();
        }
        Type typeOfT = new TypeToken<List<Fansub>>() {
        }.getType();
        GsonBuilder gsonBuilder = new GsonBuilder();
        gsonBuilder.setFieldNamingPolicy(FieldNamingPolicy.LOWER_CASE_WITH_UNDERSCORES);
        return gsonBuilder.create().fromJson(json, typeOfT);
    }
}
