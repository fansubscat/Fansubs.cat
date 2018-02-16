package cat.fansubs.app.adapters;

import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.request.RequestOptions;

import java.util.List;

import cat.fansubs.app.R;
import cat.fansubs.app.beans.Fansub;

public class FansubsAdapter extends RecyclerView.Adapter<FansubsAdapter.FansubViewHolder> {
    private List<Fansub> fansubList;
    private FansubsListListener fansubsListListener;

    public FansubsAdapter(List<Fansub> fansubList, FansubsListListener fansubsListListener) {
        this.fansubList = fansubList;
        this.fansubsListListener = fansubsListListener;
    }

    @Override
    public FansubViewHolder onCreateViewHolder(ViewGroup viewGroup, int viewType) {
        View itemView = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_fansub, viewGroup, false);
        return new FansubViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(final FansubViewHolder viewHolder, int position) {
        Fansub item = fansubList.get(position);
        viewHolder.name.setText(item.getName());
        viewHolder.url.setText(item.getUrl() != null ? item.getUrl() : item.getArchiveUrl());
        Glide.with(viewHolder.image.getContext()).load(item.getIconUrl())
                .apply(new RequestOptions().placeholder(R.color.transparent).error(R.color.transparent))
                .into(viewHolder.image);
        viewHolder.itemView.setTag(item);
        viewHolder.itemView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                fansubsListListener.onFansubItemClicked((Fansub) v.getTag());
            }
        });
    }

    @Override
    public int getItemCount() {
        return fansubList.size();
    }

    class FansubViewHolder extends RecyclerView.ViewHolder {
        private TextView name;
        private ImageView image;
        private TextView url;

        FansubViewHolder(View view) {
            super(view);
            name = view.findViewById(R.id.fansub_name);
            image = view.findViewById(R.id.fansub_image);
            url = view.findViewById(R.id.fansub_url);
        }
    }

    public interface FansubsListListener {
        void onFansubItemClicked(Fansub fansub);
    }
}
