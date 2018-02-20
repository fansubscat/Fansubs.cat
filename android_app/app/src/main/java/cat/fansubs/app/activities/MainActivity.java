package cat.fansubs.app.activities;

import android.app.NotificationManager;
import android.content.Context;
import android.content.Intent;
import android.content.res.Configuration;
import android.graphics.drawable.Drawable;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.design.widget.NavigationView;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.content.ContextCompat;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.ImageView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.DataSource;
import com.bumptech.glide.load.engine.GlideException;
import com.bumptech.glide.request.RequestListener;
import com.bumptech.glide.request.RequestOptions;
import com.bumptech.glide.request.target.Target;

import java.util.ArrayList;
import java.util.List;

import cat.fansubs.app.R;
import cat.fansubs.app.beans.Fansub;
import cat.fansubs.app.beans.News;
import cat.fansubs.app.beans.UnreadPush;
import cat.fansubs.app.components.CustomActionBarDrawerToggle;
import cat.fansubs.app.fragments.BackableFragment;
import cat.fansubs.app.fragments.FansubsFragment;
import cat.fansubs.app.fragments.MainFragment;
import cat.fansubs.app.fragments.NewsFragment;
import cat.fansubs.app.serveraccess.ServerAccess;
import cat.fansubs.app.serveraccess.model.base.ServerResponse;
import cat.fansubs.app.utils.DataUtils;
import cat.fansubs.app.utils.SharedPreferencesUtils;
import cat.fansubs.app.utils.UiUtils;

public class MainActivity extends AppCompatActivity {
    private static final String TAG = "MainActivity";

    private static final String STATE_DRAWER_TOGGLE_STATE = "drawerToggleState";

    private DrawerLayout drawerLayout;
    private NavigationView navigationView;
    private CustomActionBarDrawerToggle drawerToggle;
    private boolean isStopped;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        getWindow().setBackgroundDrawableResource(R.color.app_background);
        getWindow().setStatusBarColor(ContextCompat.getColor(this, android.R.color.transparent));

        //Remove all pending push notifications
        DataUtils.storeUnreadPushes(new ArrayList<UnreadPush>());

        //And cancel notifications
        NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
        if (notificationManager != null) {
            notificationManager.cancel(0);
        }

        setContentView(R.layout.activity_main);

        //Views
        Toolbar toolbar = findViewById(R.id.toolbar);
        drawerLayout = findViewById(R.id.drawer_layout);
        navigationView = findViewById(R.id.navigation_view);

        //Toolbar init
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        }

        drawerToggle = new CustomActionBarDrawerToggle(this, drawerLayout, R.string.menu_drawer_open, R.string.menu_drawer_back, toolbar, findViewById(R.id.search_help_layout));
        drawerLayout.addDrawerListener(drawerToggle);
        drawerLayout.setStatusBarBackgroundColor(ContextCompat.getColor(this, R.color.color_primary_dark));

        resetNavigationView(DataUtils.retrieveFansubs());

        //Set up last shown view (only if we are being created from scratch)
        if (savedInstanceState == null) {
            getSupportFragmentManager().beginTransaction().replace(R.id.main_container, new MainFragment()).commit();
            drawerToggle.setCurrentState(CustomActionBarDrawerToggle.State.MENU);
        } else {
            drawerToggle.setCurrentState((CustomActionBarDrawerToggle.State) savedInstanceState.getSerializable(STATE_DRAWER_TOGGLE_STATE));
        }

        loadRandomImageInHeader();

        navigationView.getHeaderView(0).setOnClickListener(new View.OnClickListener() {
            int count = 0;

            @Override
            public void onClick(View view) {
                count++;
                if (count % 13 == 0) {
                    SharedPreferencesUtils.setBoolean(SharedPreferencesUtils.EASTER_EGG_ENABLED, !SharedPreferencesUtils.getBoolean(SharedPreferencesUtils.EASTER_EGG_ENABLED));
                    loadRandomImageInHeader();
                }
            }
        });

        //Load fansubs list (and cache it locally).
        new AsyncTask<Void, Void, List<Fansub>>() {

            @Override
            protected List<Fansub> doInBackground(Void... params) {
                if (UiUtils.isOnline()) {
                    ServerResponse<Fansub> fansubServerResponse = ServerAccess.getFansubs();
                    if (fansubServerResponse.getStatus().equals(ServerAccess.STATUS_OK)) {
                        return fansubServerResponse.getResult();
                    }
                }
                return null;
            }

            @Override
            protected void onPostExecute(List<Fansub> fansubs) {
                if (fansubs != null) {
                    DataUtils.storeFansubs(fansubs);
                } else {
                    resetNavigationView(DataUtils.retrieveFansubs());
                }
            }
        }.execute();
    }

    private void loadRandomImageInHeader() {
        Glide.with(this).load(DataUtils.getRandomImageUrl())
                .apply(new RequestOptions().placeholder(R.color.color_primary).error(R.color.color_primary))
                .into((ImageView) navigationView.getHeaderView(0).findViewById(R.id.drawer_header));
    }

    @Override
    protected void onStart() {
        super.onStart();
        isStopped = false;
    }

    @Override
    protected void onResume() {
        super.onResume();
        updateTintingForDrawer();
    }

    @Override
    protected void onSaveInstanceState(Bundle outState) {
        super.onSaveInstanceState(outState);
        outState.putSerializable(STATE_DRAWER_TOGGLE_STATE, drawerToggle.getCurrentState());
    }

    @Override
    public void onConfigurationChanged(Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
        //Needed for the drawer
        drawerToggle.onConfigurationChanged(newConfig);
    }

    @Override
    protected void onStop() {
        isStopped = true;
        super.onStop();
    }

    private void updateTintingForDrawer() {
        Menu menu = navigationView.getMenu();
        for (int i = 0; i < menu.size(); i++) {
            MenuItem menuItem = menu.getItem(i);
            if (menuItem.getIcon() != null) {
                if (menuItem.isChecked()) {
                    menuItem.getIcon().setTint(ContextCompat.getColor(this, R.color.color_primary));
                } else if (menuItem.isCheckable()) {
                    menuItem.getIcon().setTint(ContextCompat.getColor(this, R.color.menu_item_unchecked));
                }
            }
        }
    }

    private void resetNavigationView(List<Fansub> fansubs) {
        navigationView.setItemIconTintList(null);
        navigationView.getMenu().clear();
        navigationView.inflateMenu(R.menu.menu_drawer);
        for (Fansub fansub : fansubs) {
            if (fansub.isVisible() && !fansub.isOwn() && fansub.isActive()) {
                MenuItem menuItem = navigationView.getMenu().add(R.id.active_fansubs_group, fansub.getId().hashCode(), 1, fansub.getName());
                menuItem.setIcon(R.color.transparent);
                menuItem.setCheckable(false);
                loadMenuIcon(menuItem, fansub.getIconUrl());
            }
        }

        Fragment currentFragment = getSupportFragmentManager().findFragmentById(R.id.main_container);
        if (currentFragment instanceof FansubsFragment) {
            navigationView.setCheckedItem(R.id.menu_other_fansubs);
        } else {
            navigationView.setCheckedItem(R.id.menu_news);
        }

        navigationView.setNavigationItemSelectedListener(new NavigationView.OnNavigationItemSelectedListener() {
            @Override
            public boolean onNavigationItemSelected(@NonNull MenuItem menuItem) {
                if (!isStopped) {
                    if (!menuItem.isChecked()) {
                        switch (menuItem.getItemId()) {
                            case R.id.menu_news:
                                showNews();
                                break;
                            case R.id.menu_other_fansubs:
                                showAllFansubs();
                                break;
                            case R.id.menu_notifications:
                                showNotifications();
                                break;
                            case R.id.menu_share:
                                showShare();
                                break;
                            case R.id.menu_about:
                                showAbout();
                                break;
                            default:
                                //Open fansub link
                                openFansub(DataUtils.getFansubByHashcodeId(menuItem.getItemId()));
                                break;
                        }
                    }
                    drawerLayout.closeDrawers();
                }
                return false;
            }
        });
    }

    private void showNews() {
        Fragment currentFragment = getSupportFragmentManager().findFragmentById(R.id.main_container);
        currentFragment.setExitTransition(null);
        currentFragment.setEnterTransition(null);
        MainFragment mainFragment = new MainFragment();
        FragmentTransaction transaction = getSupportFragmentManager().beginTransaction();
        try {
            transaction.replace(R.id.main_container, mainFragment).commit();
            invalidateOptionsMenu();
        } catch (IllegalStateException e) {
            Log.e(TAG, "Could not show news due to commit failing on the fragment", e);
        }

        setMenuSelection(R.id.menu_news);
    }

    private void showAllFansubs() {
        Fragment currentFragment = getSupportFragmentManager().findFragmentById(R.id.main_container);
        currentFragment.setExitTransition(null);
        currentFragment.setEnterTransition(null);
        FansubsFragment fansubsFragment = new FansubsFragment();
        FragmentTransaction transaction = getSupportFragmentManager().beginTransaction();
        try {
            transaction.replace(R.id.main_container, fansubsFragment).commit();
            invalidateOptionsMenu();
        } catch (IllegalStateException e) {
            Log.e(TAG, "Could not show fansubs due to commit failing on the fragment", e);
        }

        setMenuSelection(R.id.menu_other_fansubs);
    }

    private void showNotifications() {
        startActivity(new Intent(this, NotificationsActivity.class));
    }

    private void showShare() {
        Intent sendIntent = new Intent();
        sendIntent.setAction(Intent.ACTION_SEND);
        sendIntent.putExtra(Intent.EXTRA_SUBJECT, getString(R.string.share_title));
        sendIntent.putExtra(Intent.EXTRA_TEXT, getString(R.string.share_link));
        sendIntent.setType("text/plain");

        Intent chooser = Intent.createChooser(sendIntent, getString(R.string.share_chooser_title));
        startActivity(chooser);
    }

    private void showAbout() {
        startActivity(new Intent(this, AboutActivity.class));
    }

    private void setMenuSelection(int resource) {
        Menu m = navigationView.getMenu();
        for (int i = 0; i < m.size(); i++) {
            MenuItem mi = m.getItem(i);
            mi.setChecked(false);
        }
        navigationView.setCheckedItem(resource);
        updateTintingForDrawer();
    }

    private void loadMenuIcon(final MenuItem menuItem, String url) {
        Glide.with(this).load(url).listener(new RequestListener<Drawable>() {
            @Override
            public boolean onLoadFailed(@Nullable GlideException e, Object model, Target<Drawable> target, boolean isFirstResource) {
                return false;
            }

            @Override
            public boolean onResourceReady(Drawable resource, Object model, Target<Drawable> target, DataSource dataSource, boolean isFirstResource) {
                menuItem.setIcon(resource);
                return true;
            }
        }).preload();
    }

    public boolean onOptionsItemSelected(MenuItem item) {
        //Needed for the drawerpress
        return drawerToggle.onOptionsItemSelected(item) || super.onOptionsItemSelected(item);
    }

    @Override
    public void onBackPressed() {
        if (!isStopped) {
            if (drawerToggle.getCurrentState() == CustomActionBarDrawerToggle.State.ACTIONVIEW_UP) { //We are searching
                onActionViewClosed();
            } else {
                Fragment currentFragment = getSupportFragmentManager().findFragmentById(R.id.main_container);
                if (drawerLayout.isDrawerOpen(GravityCompat.START)) {
                    drawerLayout.closeDrawers();
                } else {
                    if ((currentFragment instanceof BackableFragment) && !((BackableFragment) currentFragment).onBackPressed()) {
                        if (getSupportFragmentManager().getBackStackEntryCount() == 1) { //We are going back to a top-level screen
                            super.onBackPressed();
                            currentFragment = getSupportFragmentManager().findFragmentById(R.id.main_container);
                            if (currentFragment instanceof MainFragment && ((MainFragment) currentFragment).isSearching()) {
                                drawerToggle.animateToState(CustomActionBarDrawerToggle.State.UP);
                            } else {
                                drawerToggle.animateToState(CustomActionBarDrawerToggle.State.MENU);
                            }
                        } else if (getSupportFragmentManager().getBackStackEntryCount() == 0 && !(currentFragment instanceof MainFragment)) {
                            showNews();
                        } else {//We are in a top-level screen OR on a deeper than 1 hierarchy
                            super.onBackPressed();
                        }
                    } else if (!(currentFragment instanceof BackableFragment)) {
                        super.onBackPressed();
                    }
                }
            }
        }
    }

    public void openNews(News news) {
        if (!isStopped) {
            NewsFragment newsFragment = new NewsFragment();

            Bundle bundle = new Bundle();
            bundle.putSerializable(NewsFragment.PARAM_NEWS, news);

            newsFragment.setArguments(bundle);
            FragmentTransaction transaction = getSupportFragmentManager().beginTransaction();
            try {
                transaction.replace(R.id.main_container, newsFragment).addToBackStack(null).commit();
                drawerToggle.animateToState(CustomActionBarDrawerToggle.State.UP);
                invalidateOptionsMenu();
                //TODO: In the future, if we implement read/unread, mark here as read.
                //Right now it can't be done because a news element does not have a specific id.
            } catch (IllegalStateException e) {
                Log.e(TAG, "Could not open news due to commit failing on the fragment", e);
            }
        }
    }

    public void openFansub(Fansub fansub) {
        UiUtils.openUrl(MainActivity.this, fansub.isHistorical() ? fansub.getArchiveUrl() : fansub.getUrl());
    }

    public void onFilterChanged() {
        Fragment fragment = getSupportFragmentManager().findFragmentById(R.id.main_container);
        if (fragment instanceof MainFragment) {
            ((MainFragment) fragment).refreshNews(false);
        }
    }

    public void onActionViewClosed() {
        if (getSupportFragmentManager().findFragmentById(R.id.main_container) instanceof MainFragment) {
            ((MainFragment) getSupportFragmentManager().findFragmentById(R.id.main_container)).closeSearch(false);
        }
    }

    public void onSearchViewCollapsed(boolean isOpeningResult, boolean isSearchEmpty) {
        if (!isOpeningResult) {
            if (isSearchEmpty) {
                drawerToggle.animateToState(CustomActionBarDrawerToggle.State.MENU);
            } else {
                drawerToggle.animateToState(CustomActionBarDrawerToggle.State.UP);
            }
        }
    }

    public void setUpNavigation() {
        drawerToggle.animateToState(CustomActionBarDrawerToggle.State.UP);
    }

    public void setActionViewUpNavigation() {
        drawerToggle.animateToState(CustomActionBarDrawerToggle.State.ACTIONVIEW_UP);
    }

    public void setMenuNavigation() {
        drawerToggle.animateToState(CustomActionBarDrawerToggle.State.MENU);
    }
}
