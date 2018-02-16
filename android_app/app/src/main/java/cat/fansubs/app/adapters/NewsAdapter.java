package cat.fansubs.app.adapters;

import android.support.v7.widget.RecyclerView;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.request.RequestOptions;

import java.util.List;

import cat.fansubs.app.R;
import cat.fansubs.app.beans.News;
import cat.fansubs.app.utils.UiUtils;

public class NewsAdapter extends RecyclerView.Adapter<NewsAdapter.NewsViewHolder> {
    private List<News> newsList;
    private NewsListListener newsListListener;

    public NewsAdapter(List<News> newsList, NewsListListener newsListListener) {
        this.newsList = newsList;
        this.newsListListener = newsListListener;
    }

    @Override
    public NewsViewHolder onCreateViewHolder(ViewGroup viewGroup, int viewType) {
        View itemView = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_news, viewGroup, false);
        return new NewsViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(final NewsViewHolder viewHolder, int position) {
        News item = newsList.get(position);
        viewHolder.title.setText(item.getTitle());
        viewHolder.date.setText(UiUtils.getRelativeDate(item.getDate()));
        viewHolder.fansub.setText(item.getFansubName());
        viewHolder.contents.setText(Html.fromHtml(item.getContents()).toString().replace("\n", " ").replaceAll("\\s{2,}", " "));

        if (item.getImageUrl() != null) {
            viewHolder.image.setVisibility(View.VISIBLE);
            Glide.with(viewHolder.image.getContext()).load(item.getImageUrl())
                    .apply(new RequestOptions().placeholder(R.color.transparent).error(R.color.transparent))
                    .into(viewHolder.image);
        } else {
            viewHolder.image.setVisibility(View.GONE);
        }
        viewHolder.itemView.setTag(item);
        viewHolder.itemView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                newsListListener.onNewsItemClicked((News) v.getTag());
            }
        });
    }

    @Override
    public int getItemCount() {
        return newsList.size();
    }

    class NewsViewHolder extends RecyclerView.ViewHolder {
        private TextView title;
        private TextView date;
        private TextView fansub;
        private TextView contents;
        private ImageView image;

        NewsViewHolder(View view) {
            super(view);
            title = view.findViewById(R.id.news_title);
            date = view.findViewById(R.id.news_date);
            fansub = view.findViewById(R.id.news_fansub);
            contents = view.findViewById(R.id.news_text);
            image = view.findViewById(R.id.news_image);
        }
    }

    public interface NewsListListener {
        void onNewsItemClicked(News tag);
    }
}
