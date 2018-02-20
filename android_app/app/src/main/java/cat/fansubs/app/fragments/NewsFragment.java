package cat.fansubs.app.fragments;

import android.content.Intent;
import android.graphics.drawable.BitmapDrawable;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.ActivityOptionsCompat;
import android.support.v4.app.Fragment;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.request.RequestOptions;

import cat.fansubs.app.R;
import cat.fansubs.app.activities.ImageActivity;
import cat.fansubs.app.beans.News;
import cat.fansubs.app.utils.DataUtils;
import cat.fansubs.app.utils.UiUtils;

public class NewsFragment extends Fragment implements BackableFragment {
    public static final String PARAM_NEWS = "news";

    private News news;

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setHasOptionsMenu(true);
    }

    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_news, container, false);
        news = (News) getArguments().getSerializable(PARAM_NEWS);

        TextView title = view.findViewById(R.id.news_title);
        TextView date = view.findViewById(R.id.news_date);
        Button button = view.findViewById(R.id.news_button);
        TextView contents = view.findViewById(R.id.news_text);
        final ImageView image = view.findViewById(R.id.news_image);

        title.setText(news.getTitle());
        date.setText(UiUtils.getRelativeDate(news.getDate()));
        contents.setText(Html.fromHtml(news.getContents()));
        button.setText(getString(R.string.visit_fansub, news.getFansubName()));

        button.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                UiUtils.openUrl(getActivity(), news.getUrl());
            }
        });

        image.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (getActivity() != null) {
                    DataUtils.setBitmap(((BitmapDrawable) image.getDrawable()).getBitmap());
                    Intent intent = new Intent(getActivity(), ImageActivity.class);
                    Bundle bundle = new Bundle();
                    bundle.putString(ImageActivity.IMAGE_URL, news.getImageUrl());
                    intent.putExtras(bundle);
                    ActivityOptionsCompat options = ActivityOptionsCompat.
                            makeSceneTransitionAnimation(getActivity(), view, "image");
                    getActivity().getWindow().setExitTransition(null);
                    startActivity(intent, options.toBundle());
                }
            }
        });

        if (news.getImageUrl() != null) {
            image.setVisibility(View.VISIBLE);
            Glide.with(image.getContext()).load(news.getImageUrl())
                    .apply(new RequestOptions().placeholder(R.color.transparent).error(R.color.transparent))
                    .into(image);
        } else {
            image.setVisibility(View.GONE);
        }
        return view;
    }

    @Override
    public void onResume() {
        super.onResume();
        if (getActivity() != null) {
            getActivity().setTitle(news.getFansubName());
        }
    }

    @Override
    public void onCreateOptionsMenu(Menu menu, MenuInflater inflater) {
        inflater.inflate(R.menu.menu_news, menu);
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case R.id.action_share:
                Intent sendIntent = new Intent();
                sendIntent.setAction(Intent.ACTION_SEND);
                sendIntent.putExtra(Intent.EXTRA_SUBJECT, news.getTitle());
                sendIntent.putExtra(Intent.EXTRA_TEXT, news.getUrl());
                sendIntent.setType("text/plain");

                Intent chooser = Intent.createChooser(sendIntent, getString(R.string.share_chooser_title));
                startActivity(chooser);
                return true;
        }
        return super.onOptionsItemSelected(item);
    }

    @Override
    public boolean onBackPressed() {
        return false;
    }
}
