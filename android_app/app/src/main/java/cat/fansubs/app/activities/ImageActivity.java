package cat.fansubs.app.activities;

import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.MenuItem;
import android.widget.ImageView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.request.RequestOptions;

import cat.fansubs.app.R;
import cat.fansubs.app.utils.DataUtils;

public class ImageActivity extends AppCompatActivity {
    public static String IMAGE_URL = "image_url";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_image);

        getWindow().setEnterTransition(null);

        String imageUrl = getIntent().getStringExtra(IMAGE_URL);

        setTitle(R.string.image_title);

        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setHomeActionContentDescription(R.string.menu_drawer_back);
        }

        ImageView image = findViewById(R.id.image);
        if (DataUtils.getBitmap() != null) {
            image.setImageBitmap(DataUtils.getBitmap());
            DataUtils.setBitmap(null);
        } else {
            Glide.with(this)
                    .load(imageUrl)
                    .apply(new RequestOptions().placeholder(R.color.transparent).error(R.color.transparent))
                    .into(image);
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
