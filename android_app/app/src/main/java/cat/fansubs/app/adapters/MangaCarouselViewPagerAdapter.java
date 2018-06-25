package cat.fansubs.app.adapters;

import android.content.Context;
import android.support.annotation.NonNull;
import android.support.v4.view.PagerAdapter;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.request.RequestOptions;

import java.util.List;

import cat.fansubs.app.R;
import cat.fansubs.app.beans.MangaImage;

public class MangaCarouselViewPagerAdapter extends PagerAdapter {

    private Context context;
    private List<MangaImage> images;

    public MangaCarouselViewPagerAdapter(Context context, List<MangaImage> images) {
        this.context = context;
        this.images = images;
    }

    @NonNull
    @Override
    public Object instantiateItem(@NonNull ViewGroup collection, int position) {
        LayoutInflater inflater = LayoutInflater.from(context);
        View view = inflater.inflate(R.layout.item_carousel_image, collection, false);
        collection.addView(view);

        MangaImage image = images.get(position);

        ImageView imageView = view.findViewById(R.id.carousel_image);

        Glide.with(imageView.getContext()).load(image.getElementUrl())
                .apply(new RequestOptions().placeholder(R.color.transparent).error(R.color.transparent))
                .into(imageView);

        return view;
    }

    @Override
    public int getCount() {
        return images.size();
    }

    @Override
    public void destroyItem(@NonNull ViewGroup collection, int position, @NonNull Object view) {
        collection.removeView((View) view);
    }

    @Override
    public boolean isViewFromObject(@NonNull View view, @NonNull Object object) {
        return view == object;
    }
}