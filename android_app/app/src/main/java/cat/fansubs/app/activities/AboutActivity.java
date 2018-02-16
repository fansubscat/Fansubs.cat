package cat.fansubs.app.activities;

import android.content.Intent;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.MenuItem;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;

import cat.fansubs.app.R;
import cat.fansubs.app.utils.Constants;
import cat.fansubs.app.utils.UiUtils;

public final class AboutActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_about);

        setTitle(R.string.about_title);

        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setHomeActionContentDescription(R.string.menu_drawer_back);
        }

        TextView aboutAppName = findViewById(R.id.about_app_name);

        PackageInfo packageInfo;
        try {
            packageInfo = getPackageManager().getPackageInfo(getPackageName(), 0);
            aboutAppName.setText(String.format("%s %s", getString(R.string.app_name), packageInfo.versionName));
        } catch (PackageManager.NameNotFoundException e) {
            aboutAppName.setText(getString(R.string.app_name));
        }

        findViewById(R.id.send_suggestion).setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View view) {
                Intent emailIntent = new Intent(android.content.Intent.ACTION_SENDTO, Uri.fromParts("mailto", Constants.EMAIL_SUGGESTIONS, null));

                String versionName;

                try {
                    PackageInfo packageInfo = getPackageManager().getPackageInfo(getPackageName(), 0);
                    versionName = packageInfo.versionName;
                } catch (PackageManager.NameNotFoundException e) {
                    versionName = getString(R.string.unknown_version);
                }

                emailIntent.putExtra(android.content.Intent.EXTRA_SUBJECT, getString(R.string.suggestion_subject, versionName));

                if (getPackageManager().resolveActivity(emailIntent, 0) != null) {
                    startActivity(emailIntent);
                } else {
                    Toast.makeText(AboutActivity.this, R.string.suggestion_email_intent_failed, Toast.LENGTH_SHORT).show();
                }
            }
        });

        findViewById(R.id.about_github).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                UiUtils.openUrl(AboutActivity.this, Constants.GITHUB_URL);
            }
        });
        findViewById(R.id.about_opensource_licenses).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                startActivity(new Intent(AboutActivity.this, LicensesActivity.class));
            }
        });
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