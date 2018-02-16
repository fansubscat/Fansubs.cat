package cat.fansubs.app.utils;

import android.content.pm.PackageManager;

import java.io.IOException;

import cat.fansubs.app.FansubsApplication;
import okhttp3.Interceptor;
import okhttp3.Request;
import okhttp3.Response;

public class UserAgentHeaderInterceptor implements Interceptor {

    @Override
    public Response intercept(Chain chain) throws IOException {
        String version;
        try {
            version = FansubsApplication.getInstance().getPackageManager().getPackageInfo(FansubsApplication.getInstance().getPackageName(), 0).versionName;
        } catch (PackageManager.NameNotFoundException e) {
            version = "Unknown";
        }

        Request.Builder builder = chain.request().newBuilder()
                .header("User-Agent", "FansubsCatApp/Android/" + version + " [" + System.getProperty("http.agent") + "]");
        return chain.proceed(builder.build());
    }
}
