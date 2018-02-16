package cat.fansubs.app.activities;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.SwitchCompat;
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.CompoundButton;
import android.widget.EditText;
import android.widget.TextView;

import java.util.List;

import cat.fansubs.app.R;
import cat.fansubs.app.utils.DataUtils;
import cat.fansubs.app.utils.SharedPreferencesUtils;

public final class NotificationsActivity extends AppCompatActivity {

    private ViewGroup textsLayout;
    private ViewGroup textsContainer;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_notifications);

        setTitle(R.string.notifications_title);

        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setHomeActionContentDescription(R.string.menu_drawer_back);
        }

        SwitchCompat notificationsSwitch = findViewById(R.id.notifications_fansubs_enabled);
        notificationsSwitch.setChecked(SharedPreferencesUtils.getBoolean(SharedPreferencesUtils.NOTIFICATIONS_ENABLED, true));
        notificationsSwitch.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton compoundButton, boolean b) {
                SharedPreferencesUtils.setBoolean(SharedPreferencesUtils.NOTIFICATIONS_ENABLED, b);
                setControlsEnabled(SharedPreferencesUtils.getBoolean(SharedPreferencesUtils.NOTIFICATIONS_ENABLED, true), textsLayout);
            }
        });

        textsLayout = findViewById(R.id.notification_texts_layout);
        textsContainer = findViewById(R.id.notifications_texts_container);

        updateTextFilters();

        findViewById(R.id.notifications_texts_add).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                @SuppressLint("InflateParams")
                View dialogView = getLayoutInflater().inflate(R.layout.dialog_add_text, null, false);
                final EditText editText = dialogView.findViewById(R.id.text);

                new AlertDialog.Builder(NotificationsActivity.this).setTitle(R.string.add_text_title)
                        .setView(dialogView)
                        .setPositiveButton(R.string.ok, new DialogInterface.OnClickListener() {
                            public void onClick(DialogInterface dialog, int whichButton) {
                                dialog.dismiss();
                                if (!editText.getText().toString().isEmpty()) {
                                    final List<String> textFilters = DataUtils.retrieveNotificationTextFilters();
                                    textFilters.add(editText.getText().toString());
                                    DataUtils.storeNotificationTextFilters(textFilters);
                                    updateTextFilters();
                                }
                            }
                        }).setNegativeButton(R.string.cancel, new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int whichButton) {
                        dialog.dismiss();
                    }
                }).create().show();
            }
        });
    }

    private void updateTextFilters() {
        final List<String> textFilters = DataUtils.retrieveNotificationTextFilters();

        textsContainer.removeAllViews();
        for (final String textFilter : textFilters) {
            View view = LayoutInflater.from(this).inflate(R.layout.item_text_filter, textsContainer, false);

            ((TextView) view.findViewById(R.id.text)).setText(textFilter);
            view.findViewById(R.id.remove_button).setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    textFilters.remove(textFilter);
                    DataUtils.storeNotificationTextFilters(textFilters);
                    updateTextFilters();
                }
            });

            textsContainer.addView(view);
            View.inflate(this, R.layout.generic_divider_gray, textsContainer);
        }

        setControlsEnabled(SharedPreferencesUtils.getBoolean(SharedPreferencesUtils.NOTIFICATIONS_ENABLED, true), textsLayout);
    }

    private void setControlsEnabled(boolean enable, ViewGroup vg) {
        for (int i = 0; i < vg.getChildCount(); i++) {
            View child = vg.getChildAt(i);
            child.setEnabled(enable);
            if (child instanceof ViewGroup) {
                setControlsEnabled(enable, (ViewGroup) child);
            }
        }
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case android.R.id.home:
                onBackPressed();
                return true;
        }
        return super.onOptionsItemSelected(item);
    }
}