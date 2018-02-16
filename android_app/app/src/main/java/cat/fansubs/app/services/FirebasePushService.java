package cat.fansubs.app.services;

import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.graphics.drawable.BitmapDrawable;
import android.os.Build;
import android.support.v4.app.NotificationCompat;
import android.support.v4.content.ContextCompat;
import android.util.Log;

import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

import java.util.List;
import java.util.Locale;

import cat.fansubs.app.R;
import cat.fansubs.app.activities.MainActivity;
import cat.fansubs.app.beans.UnreadPush;
import cat.fansubs.app.utils.DataUtils;
import cat.fansubs.app.utils.SharedPreferencesUtils;

public class FirebasePushService extends FirebaseMessagingService {
    private static final String TAG = "FirebasePushService";

    private static final String CHANNEL_ID = "fansubscat_news";

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        if (remoteMessage.getData() != null) {
            String title = remoteMessage.getData().get("title");
            String fansub = remoteMessage.getData().get("fansub");
            String fansubId = remoteMessage.getData().get("fansub_id");

            if (SharedPreferencesUtils.getBoolean(SharedPreferencesUtils.NOTIFICATIONS_ENABLED, true)) {
                List<String> fansubFilters = DataUtils.retrieveFilterFansubIds();

                boolean matchesFansubFilter = true;
                if (!fansubFilters.isEmpty()) {
                    matchesFansubFilter = false;
                    for (String fansubFilter : fansubFilters) {
                        if (fansubId.equals(fansubFilter)) {
                            matchesFansubFilter = true;
                            break;
                        }
                    }
                }

                if (matchesFansubFilter) {
                    List<String> textFilters = DataUtils.retrieveNotificationTextFilters();

                    boolean matchesFilter = true;
                    if (!textFilters.isEmpty()) {
                        matchesFilter = false;
                        for (String textFilter : textFilters) {
                            if (title.toLowerCase(Locale.getDefault()).contains(textFilter.toLowerCase(Locale.getDefault()))) {
                                matchesFilter = true;
                                break;
                            }
                        }
                    }

                    if (matchesFilter) {
                        Log.d(TAG, "Received new push and matches user filters, parsing.");

                        List<UnreadPush> unreadPushes = DataUtils.retrieveUnreadPushes();
                        unreadPushes.add(new UnreadPush(fansub, title, fansubId));
                        DataUtils.storeUnreadPushes(unreadPushes);

                        String notificationTitle;
                        StringBuilder notificationContents = new StringBuilder();

                        boolean allSameFansub = true;
                        String previousFansubId = null;

                        for (UnreadPush unreadPush : unreadPushes) {
                            if (previousFansubId != null && !previousFansubId.equals(unreadPush.getFansubId())) {
                                allSameFansub = false;
                                break;
                            } else {
                                previousFansubId = unreadPush.getFansubId();
                            }
                        }

                        for (UnreadPush unreadPush : unreadPushes) {
                            if (notificationContents.length() == 0) {
                                if (allSameFansub) {
                                    notificationContents = new StringBuilder(unreadPush.getTitle());
                                } else {
                                    notificationContents = new StringBuilder(unreadPush.getFansub()).append(": ").append(unreadPush.getTitle());
                                }
                            } else {
                                if (allSameFansub) {
                                    notificationContents.append("\n").append(unreadPush.getTitle());
                                } else {
                                    notificationContents.append("\n").append(unreadPush.getFansub()).append(": ").append(unreadPush.getTitle());
                                }
                            }
                        }

                        if (allSameFansub) {
                            notificationTitle = getResources().getQuantityString(R.plurals.push_title_fansub, unreadPushes.size(), fansub, unreadPushes.size());
                        } else {
                            notificationTitle = getResources().getQuantityString(R.plurals.push_title_fansubs, unreadPushes.size(), unreadPushes.size());
                        }

                        sendNotification(notificationTitle, notificationContents.toString());
                    } else {
                        Log.d(TAG, "Ignoring received push because it doesn't match user text filters.");
                    }
                } else {
                    Log.d(TAG, "Ignoring received push because it doesn't match user fansub filters.");
                }
            } else {
                Log.d(TAG, "Ignoring received push because notifications are disabled by the user.");
            }
        }
    }

    private void sendNotification(String title, String contents) {
        Intent intent = new Intent(this, MainActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        PendingIntent pendingIntent = PendingIntent.getActivity(this, 0, intent, PendingIntent.FLAG_ONE_SHOT);


        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
            if (notificationManager != null) {
                NotificationChannel mChannel = new NotificationChannel(CHANNEL_ID,
                        getString(R.string.notification_channel_name), NotificationManager.IMPORTANCE_DEFAULT);
                notificationManager.createNotificationChannel(mChannel);
            }
        }

        NotificationCompat.Builder notificationBuilder =
                new NotificationCompat.Builder(this, CHANNEL_ID)
                        .setSmallIcon(R.drawable.ic_vector_news)
                        .setLargeIcon(((BitmapDrawable) getResources().getDrawable(R.mipmap.ic_launcher)).getBitmap())
                        .setContentTitle(title)
                        .setContentText(contents)
                        .setAutoCancel(true)
                        .setColor(ContextCompat.getColor(this, R.color.color_primary))
                        .setDefaults(NotificationCompat.DEFAULT_ALL)
                        .setContentIntent(pendingIntent)
                        .setStyle(new NotificationCompat.BigTextStyle().bigText(contents));

        NotificationManager notificationManager =
                (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

        if (notificationManager != null) {
            notificationManager.notify(0, notificationBuilder.build());
        }
    }
}
