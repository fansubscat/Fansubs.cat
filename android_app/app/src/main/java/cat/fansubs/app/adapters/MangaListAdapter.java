package cat.fansubs.app.adapters;

import android.support.v7.widget.RecyclerView;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.request.RequestOptions;

import java.util.List;

import cat.fansubs.app.FansubsApplication;
import cat.fansubs.app.R;
import cat.fansubs.app.beans.MangaCategory;
import cat.fansubs.app.beans.MangaImage;
import cat.fansubs.app.utils.GenericRecyclerViewViewHolder;

public class MangaListAdapter extends RecyclerView.Adapter<GenericRecyclerViewViewHolder> {
    private static final int TYPE_CATEGORY = 1;
    private static final int TYPE_IMAGE = 2;
    private static final int TYPE_HEADER = 0;

    private boolean firstLevel;
    private List<Object> elementsList;
    private MangaListListener mangaListListener;

    public MangaListAdapter(boolean firstLevel, List<Object> elementsList, MangaListListener mangaListListener) {
        this.firstLevel = firstLevel;
        this.elementsList = elementsList;
        this.mangaListListener = mangaListListener;
    }

    @Override
    public GenericRecyclerViewViewHolder onCreateViewHolder(ViewGroup viewGroup, int viewType) {
        if (viewType == TYPE_CATEGORY) {
            View itemView = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_manga_category, viewGroup, false);
            GenericRecyclerViewViewHolder viewHolder = new GenericRecyclerViewViewHolder(itemView);
            viewHolder.setView("title", itemView.findViewById(R.id.manga_category_title));
            viewHolder.setView("info", itemView.findViewById(R.id.manga_category_info));
            viewHolder.setView("contents", itemView.findViewById(R.id.manga_category_contents));
            viewHolder.setView("image", itemView.findViewById(R.id.manga_category_image));
            return viewHolder;
        } else if (viewType == TYPE_IMAGE) {
            View itemView = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_manga_image, viewGroup, false);
            GenericRecyclerViewViewHolder viewHolder = new GenericRecyclerViewViewHolder(itemView);
            viewHolder.setView("image", itemView.findViewById(R.id.manga_image_image));
            return viewHolder;
        } else if (!firstLevel) {
            View itemView = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_manga_header, viewGroup, false);
            GenericRecyclerViewViewHolder viewHolder = new GenericRecyclerViewViewHolder(itemView);
            viewHolder.setView("title", itemView.findViewById(R.id.manga_header_title));
            viewHolder.setView("contents", itemView.findViewById(R.id.manga_header_contents));
            return viewHolder;
        } else {
            throw new RuntimeException("Unexpected object type");
        }
    }

    @Override
    public void onBindViewHolder(final GenericRecyclerViewViewHolder viewHolder, int position) {
        int viewType = getItemViewType(position);
        if (viewType == TYPE_CATEGORY) {
            MangaCategory item = (MangaCategory) elementsList.get(position);
            viewHolder.getView("title", TextView.class).setText(item.getName());

            String text = null;

            if (item.getTotalNbImages() > 0) {
                text = FansubsApplication.getInstance().getResources().getQuantityString(R.plurals.manga_category_info_images, item.getTotalNbImages().intValue(), item.getTotalNbImages());
            }
            if (item.getNbCategories() > 0) {
                if (text != null) {
                    text = text.concat(" · ");
                    text = text.concat(FansubsApplication.getInstance().getResources().getQuantityString(R.plurals.manga_category_info_subalbums, item.getNbCategories().intValue(), item.getNbCategories()));
                } else {
                    text = FansubsApplication.getInstance().getResources().getQuantityString(R.plurals.manga_category_info_subalbums, item.getTotalNbImages().intValue(), item.getTotalNbImages());
                }
            }

            viewHolder.getView("info", TextView.class).setText(text);
            if (!TextUtils.isEmpty(item.getComment())) {
                viewHolder.getView("contents", TextView.class).setVisibility(View.VISIBLE);
                viewHolder.getView("contents", TextView.class).setText(item.getComment());
            } else {
                viewHolder.getView("contents", TextView.class).setVisibility(View.GONE);
            }
            Glide.with(viewHolder.getView("image", ImageView.class).getContext()).load(item.getTnUrl())
                    .apply(new RequestOptions().placeholder(R.color.transparent).error(R.color.transparent))
                    .into(viewHolder.getView("image", ImageView.class));
            viewHolder.itemView.setTag(item);
            viewHolder.itemView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    mangaListListener.onMangaCategoryItemClicked((MangaCategory) v.getTag());
                }
            });
        } else if (viewType == TYPE_IMAGE) {
            MangaImage item = (MangaImage) elementsList.get(position);
            Glide.with(viewHolder.getView("image", ImageView.class).getContext()).load(item.getElementUrl())
                    .apply(new RequestOptions().placeholder(R.color.transparent).error(R.color.transparent))
                    .into(viewHolder.getView("image", ImageView.class));
            viewHolder.itemView.setTag(item);
            viewHolder.itemView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    mangaListListener.onMangaImageItemClicked((MangaImage) v.getTag());
                }
            });
        } else if (viewType == TYPE_HEADER) {
            MangaCategory item = (MangaCategory) elementsList.get(position);
            viewHolder.getView("title", TextView.class).setText(item.getName());

            if (!TextUtils.isEmpty(item.getComment())) {
                viewHolder.getView("contents", TextView.class).setVisibility(View.VISIBLE);
                viewHolder.getView("contents", TextView.class).setText(item.getComment());
            } else {
                viewHolder.getView("contents", TextView.class).setVisibility(View.GONE);
            }
        }
    }

    @Override
    public int getItemViewType(int position) {
        Object item = elementsList.get(position);
        if (position == 0 && !firstLevel) {
            return TYPE_HEADER;
        } else if (item instanceof MangaCategory) {
            return TYPE_CATEGORY;
        } else if (item instanceof MangaImage) {
            return TYPE_IMAGE;
        } else {
            throw new RuntimeException("Unexpected object type");
        }
    }

    @Override
    public int getItemCount() {
        return elementsList.size();
    }

    public interface MangaListListener {
        void onMangaCategoryItemClicked(MangaCategory mangaCategory);

        void onMangaImageItemClicked(MangaImage mangaImage);
    }
}
