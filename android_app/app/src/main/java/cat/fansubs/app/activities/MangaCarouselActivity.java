package cat.fansubs.app.activities;

import android.os.Bundle;
import android.support.v4.view.ViewPager;
import android.support.v7.app.AppCompatActivity;
import android.view.View;

import java.util.List;

import cat.fansubs.app.R;
import cat.fansubs.app.adapters.MangaCarouselViewPagerAdapter;
import cat.fansubs.app.beans.MangaImage;

public class MangaCarouselActivity extends AppCompatActivity {
    public static final String PARAM_IMAGES = "images";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_manga_carousel);

        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setHomeActionContentDescription(R.string.menu_drawer_back);
        }

        List<MangaImage> images = (List<MangaImage>) getIntent().getSerializableExtra(PARAM_IMAGES);

        ViewPager viewPager = findViewById(R.id.view_pager);
//        viewPager.setOffscreenPageLimit(5);
        viewPager.setAdapter(new MangaCarouselViewPagerAdapter(this, images));
    }

    @Override
    public void onWindowFocusChanged(boolean hasFocus) {
        super.onWindowFocusChanged(hasFocus);
        if (hasFocus) {
            getWindow().getDecorView().setSystemUiVisibility(
                    View.SYSTEM_UI_FLAG_LAYOUT_STABLE
                            | View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
                            | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN
                            | View.SYSTEM_UI_FLAG_HIDE_NAVIGATION
                            | View.SYSTEM_UI_FLAG_FULLSCREEN
                            | View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY);
        }
    }

}
