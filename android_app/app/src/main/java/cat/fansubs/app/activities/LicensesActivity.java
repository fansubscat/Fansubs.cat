package cat.fansubs.app.activities;

import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.DividerItemDecoration;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.MenuItem;
import android.view.View;

import java.util.ArrayList;
import java.util.List;

import cat.fansubs.app.R;
import cat.fansubs.app.adapters.LicensesAdapter;
import cat.fansubs.app.beans.License;
import cat.fansubs.app.utils.LicensesUtil;

public final class LicensesActivity extends AppCompatActivity {

    private View loadingLayout;
    private RecyclerView licensesList;
    private LicensesAdapter licensesAdapter;

    private List<License> licenses;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_licenses);
        setTitle(R.string.licenses_title);

        if (getSupportActionBar()!=null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setHomeActionContentDescription(R.string.menu_drawer_back);
        }

        licenses = new ArrayList<>();
        loadingLayout = findViewById(R.id.loading_layout);
        licensesList = findViewById(R.id.licenses_list);

        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(this);
        licensesList.setLayoutManager(mLayoutManager);

        licensesAdapter = new LicensesAdapter(licenses);
        licensesList.setAdapter(licensesAdapter);
        licensesList.addItemDecoration(new DividerItemDecoration(this, DividerItemDecoration.VERTICAL));

        new AsyncTask<Void, Void, List<License>>() {
            @Override
            protected List<License> doInBackground(Void... params) {
                return LicensesUtil.loadLicenses();
            }

            @Override
            protected void onPostExecute(List<License> result) {
                licenses.clear();
                licenses.addAll(result);
                licensesAdapter.notifyDataSetChanged();
                loadingLayout.setVisibility(View.GONE);
                licensesList.setVisibility(View.VISIBLE);
            }
        }.execute();
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