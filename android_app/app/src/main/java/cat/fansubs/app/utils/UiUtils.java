package cat.fansubs.app.utils;

import android.app.DownloadManager;
import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.Canvas;
import android.graphics.drawable.Drawable;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Environment;
import android.support.customtabs.CustomTabsIntent;
import android.support.v4.content.ContextCompat;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.Toast;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

import cat.fansubs.app.FansubsApplication;
import cat.fansubs.app.R;

public class UiUtils {
    public static String getRelativeDate(long date) {
        long difference = System.currentTimeMillis() / 1000 - date;

        if (difference < 60) {
            return FansubsApplication.getInstance().getResources().getQuantityString(R.plurals.date_seconds_ago, (int) difference, (int) difference);
        } else if (difference < 3600) {
            return FansubsApplication.getInstance().getResources().getQuantityString(R.plurals.date_minutes_ago, (int) difference / 60, (int) difference / 60);
        } else if (difference < 86400) {
            return FansubsApplication.getInstance().getResources().getQuantityString(R.plurals.date_hours_ago, (int) difference / 3600, (int) difference / 3600);
        } else if (difference < 2592000) {
            return FansubsApplication.getInstance().getResources().getQuantityString(R.plurals.date_days_ago, (int) difference / 86400, (int) difference / 86400);
        } else {
            Date dateObj = new Date(date * 1000);
            SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy HH:mm", Locale.US);
            return sdf.format(dateObj);
        }
    }

    public static void openUrl(Context context, String url) {
        CustomTabsIntent.Builder builder = new CustomTabsIntent.Builder();
        builder.setToolbarColor(ContextCompat.getColor(context, R.color.color_primary));
        CustomTabsIntent customTabsIntent = builder.build();
        try {
            customTabsIntent.launchUrl(context, Uri.parse(url));
        } catch (Exception e) {
            //An exception might be thrown if no activity is available to handle the intent
            //Since we can't change the code on the support library, we add a catch
            Toast.makeText(context, R.string.invalid_link, Toast.LENGTH_SHORT).show();
        }
    }

    public static Bitmap getBitmapFromVector(Drawable vectorDrawable, int sizeInPx) {
        vectorDrawable.setBounds(0, 0, sizeInPx, sizeInPx);
        Bitmap bitmap = Bitmap.createBitmap(sizeInPx, sizeInPx, Bitmap.Config.ARGB_8888);
        Canvas canvas = new Canvas(bitmap);
        vectorDrawable.draw(canvas);
        return bitmap;
    }

    public static boolean isOnline() {
        ConnectivityManager cm = (ConnectivityManager) FansubsApplication.getInstance().getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo info = null;
        if (cm != null) {
            info = cm.getActiveNetworkInfo();
        }
        return (info != null && info.isConnected());
    }

    public static void requestFocusAndShowKeyboard(final View view) {
        view.requestFocus();
        view.post(new Runnable() {
            @Override
            public void run() {
                view.post(new Runnable() {
                    @Override
                    public void run() {
                        InputMethodManager imm = (InputMethodManager) view.getContext().getSystemService(Context.INPUT_METHOD_SERVICE);
                        if (imm != null) {
                            imm.showSoftInput(view, 0);
                        }
                    }
                });
            }
        });
    }

    public static void hideKeyboard(View view) {
        InputMethodManager imm = (InputMethodManager) view.getContext().getSystemService(Context.INPUT_METHOD_SERVICE);
        if (imm != null) {
            imm.hideSoftInputFromWindow(view.getWindowToken(), 0);
        }
    }

    public static void downloadFileViaDownloadManager(String url, String downloadName, String fileName) {
        DownloadManager mgr = (DownloadManager) FansubsApplication.getInstance().getSystemService(Context.DOWNLOAD_SERVICE);
        if (mgr != null) {
            DownloadManager.Request req = new DownloadManager.Request(Uri.parse(url));
            req.setTitle(downloadName)
                    .setDescription(fileName)
                    .setDestinationInExternalPublicDir(Environment.DIRECTORY_DOWNLOADS, fileName)
                    .setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED);
            mgr.enqueue(req);
            Toast.makeText(FansubsApplication.getInstance(), R.string.must_update_app_downloading, Toast.LENGTH_SHORT).show();
        }
    }
}
