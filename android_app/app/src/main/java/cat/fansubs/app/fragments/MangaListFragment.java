package cat.fansubs.app.fragments;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.widget.DividerItemDecoration;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;

import cat.fansubs.app.R;
import cat.fansubs.app.activities.MainActivity;
import cat.fansubs.app.activities.MangaCarouselActivity;
import cat.fansubs.app.adapters.MangaListAdapter;
import cat.fansubs.app.beans.MangaCategory;
import cat.fansubs.app.beans.MangaImage;
import cat.fansubs.app.serveraccess.ServerAccess;
import cat.fansubs.app.serveraccess.model.PiwigoCategoriesResponse;
import cat.fansubs.app.serveraccess.model.PiwigoImagesResponse;
import cat.fansubs.app.serveraccess.model.base.PiwigoResponse;
import cat.fansubs.app.utils.UiUtils;

public class MangaListFragment extends Fragment implements BackableFragment {
    public static final String PARAM_CATEGORY_ID = "categoryId";
    private View loadingLayout;
    private View errorLayout;
    private SwipeRefreshLayout swipeRefreshLayout;
    private RecyclerView recyclerView;
    private View emptyLayout;
    private LinearLayoutManager linearLayoutManager;

    private MangaListAdapter adapter;
    private List<Object> elements = new ArrayList<>();

    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_manga_list, container, false);

        loadingLayout = view.findViewById(R.id.loading_layout);
        errorLayout = view.findViewById(R.id.error_layout);
        swipeRefreshLayout = view.findViewById(R.id.swipe_to_refresh);
        recyclerView = view.findViewById(R.id.recycler_view);
        emptyLayout = view.findViewById(R.id.empty_layout);

        view.findViewById(R.id.error_button_retry).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                refreshData(false);
            }
        });

        swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                refreshData(true);
            }
        });
        swipeRefreshLayout.setColorSchemeResources(R.color.color_primary);

        linearLayoutManager = new LinearLayoutManager(getActivity());
        recyclerView.setLayoutManager(linearLayoutManager);
        adapter = new MangaListAdapter((getArguments() != null && getArguments().getLong(PARAM_CATEGORY_ID, -1) == -1), elements, new MangaListAdapter.MangaListListener() {
            @Override
            public void onMangaCategoryItemClicked(MangaCategory mangaCategory) {
                if (getActivity() != null) {
                    ((MainActivity) getActivity()).showManga(mangaCategory.getId());
                }
            }

            @Override
            public void onMangaImageItemClicked(MangaImage mangaImage) {
                List<MangaImage> images = new ArrayList<>();
                for (Object element : elements) {
                    if (element instanceof MangaImage) {
                        images.add((MangaImage) element);
                    }
                }

                Intent intent = new Intent(getActivity(), MangaCarouselActivity.class);
                intent.putExtra(MangaCarouselActivity.PARAM_IMAGES, (Serializable) images);
                startActivity(intent);
            }
        });
        recyclerView.setAdapter(adapter);
        recyclerView.addItemDecoration(new DividerItemDecoration(recyclerView.getContext(), DividerItemDecoration.VERTICAL));

        if (elements.isEmpty()) {
            loadingLayout.setVisibility(View.VISIBLE);
            swipeRefreshLayout.setVisibility(View.GONE);
            errorLayout.setVisibility(View.GONE);
            refreshData(false);
        } else {
            adapter.notifyDataSetChanged();
            loadingLayout.setVisibility(View.GONE);
            swipeRefreshLayout.setVisibility(View.VISIBLE);
            errorLayout.setVisibility(View.GONE);
        }

        return view;
    }

    public void refreshData(boolean fromPullToRefresh) {
        if (!fromPullToRefresh) {
            loadingLayout.setVisibility(View.VISIBLE);
            errorLayout.setVisibility(View.GONE);
            swipeRefreshLayout.setVisibility(View.GONE);
        }
        loadData(getArguments() != null ? getArguments().getLong(PARAM_CATEGORY_ID, -1) : -1);
    }

    @Override
    public void onResume() {
        super.onResume();
        if (getActivity() != null) {
            getActivity().setTitle(R.string.manga_title);
        }
    }

    private void loadData(final long categoryId) {
        new AsyncTask<Void, Void, List<Object>>() {

            @Override
            protected List<Object> doInBackground(Void... params) {
                if (UiUtils.isOnline()) {
                    PiwigoResponse<PiwigoCategoriesResponse> categories = ServerAccess.getMangaCategories(categoryId != -1 ? categoryId : null);
                    PiwigoResponse<PiwigoImagesResponse> images;
                    if (categoryId != -1) {
                        images = ServerAccess.getMangaImages(categoryId);
                    } else {
                        images = null;
                    }
                    if (categories.getStat().equals(ServerAccess.STATUS_OK) && (images == null || images.getStat().equals(ServerAccess.STATUS_OK))) {
                        List<Object> elements = new ArrayList<>();
                        elements.addAll(categories.getResult().getCategories());
                        if (images != null) {
                            elements.addAll(images.getResult().getImages());
                        }
                        return elements;
                    }
                }
                return null;
            }

            @Override
            protected void onPostExecute(List<Object> dataResult) {
                if (swipeRefreshLayout.isRefreshing()) {
                    swipeRefreshLayout.setRefreshing(false);
                }
                if (dataResult != null) {
                    elements.clear();
                    elements.addAll(dataResult);
                    adapter.notifyDataSetChanged();
                    loadingLayout.setVisibility(View.GONE);
                    errorLayout.setVisibility(View.GONE);
                    swipeRefreshLayout.setVisibility(View.VISIBLE);
                    if (!elements.isEmpty()) {
                        recyclerView.setVisibility(View.VISIBLE);
                        emptyLayout.setVisibility(View.GONE);
                    } else {
                        recyclerView.setVisibility(View.GONE);
                        emptyLayout.setVisibility(View.VISIBLE);
                    }
                } else {
                    loadingLayout.setVisibility(View.GONE);
                    errorLayout.setVisibility(View.VISIBLE);
                    swipeRefreshLayout.setVisibility(View.GONE);
                }
            }
        }.execute();
    }

    @Override
    public boolean onBackPressed() {
        return false;
    }
}
