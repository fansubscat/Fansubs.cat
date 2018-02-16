package cat.fansubs.app;

import android.app.Application;

import com.crashlytics.android.Crashlytics;
import com.google.firebase.FirebaseApp;
import com.google.firebase.messaging.FirebaseMessaging;

import cat.ereza.customactivityoncrash.CustomActivityOnCrash;
import cat.ereza.customactivityoncrash.config.CaocConfig;
import cat.ereza.logcatreporter.LogcatReporter;
import cat.fansubs.app.utils.Constants;
import io.fabric.sdk.android.Fabric;

public class FansubsApplication extends Application {
    private static FansubsApplication instance;

    @Override
    public void onCreate() {
        super.onCreate();
        instance = this;

        //Apply CAOC config
        CaocConfig.Builder.create()
                .showErrorDetails(BuildConfig.DEBUG)
                .eventListener(new EventListener())
                .apply();

        //Install Crashlytics
        Fabric.with(this, new Crashlytics());

        LogcatReporter.install();

        String gpsVersion;
        try {
            gpsVersion = getPackageManager().getPackageInfo(Constants.GOOGLE_PLAY_SERVICES_PACKAGE, 0).versionName;
        } catch (Exception e) {
            gpsVersion = "Unknown";
        }

        Crashlytics.setString("play_services_version", gpsVersion);

        FirebaseApp.initializeApp(this);
        FirebaseMessaging.getInstance().subscribeToTopic("all");
    }

    public static FansubsApplication getInstance() {
        return instance;
    }

    public static class EventListener implements CustomActivityOnCrash.EventListener {

        @Override
        public void onLaunchErrorActivity() {
//            GoogleAnalyticsHelper.trackEvent(GoogleAnalyticsHelper.ACTION_LAUNCH_ERROR_SCREEN);
        }

        @Override
        public void onRestartAppFromErrorActivity() {
//            GoogleAnalyticsHelper.trackEvent(GoogleAnalyticsHelper.ACTION_RESTART_FROM_ERROR);
        }

        @Override
        public void onCloseAppFromErrorActivity() {
            //Will never happen
        }
    }
}
