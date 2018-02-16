package cat.fansubs.app.fragments;

import android.os.AsyncTask;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.widget.DividerItemDecoration;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.Editable;
import android.text.TextUtils;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.EditorInfo;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import java.util.ArrayList;
import java.util.List;

import cat.fansubs.app.R;
import cat.fansubs.app.activities.MainActivity;
import cat.fansubs.app.adapters.NewsAdapter;
import cat.fansubs.app.beans.News;
import cat.fansubs.app.serveraccess.ServerAccess;
import cat.fansubs.app.serveraccess.model.base.ServerResponse;
import cat.fansubs.app.utils.DataUtils;
import cat.fansubs.app.utils.UiUtils;

public class MainFragment extends Fragment implements BackableFragment {
    private static final String STATE_IS_SEARCH_OPEN = "isSearchOpen";
    private static final String STATE_IS_SEARCHING = "isSearching";
    private static final String STATE_CURRENT_SEARCH_TEXT = "currentSearchText";
    private static final String STATE_APPLIED_SEARCH_TEXT = "appliedSearchText";

    private boolean allowLoadingMoreElements;
    private int currentPage = 0;

    private View loadingLayout;
    private View errorLayout;
    private SwipeRefreshLayout swipeRefreshLayout;
    private RecyclerView recyclerView;
    private View emptyLayout;
    private LinearLayoutManager linearLayoutManager;

    private NewsAdapter adapter;
    private List<News> news = new ArrayList<>();

    private boolean isSearchOpen;
    private boolean isSearching;
    private String currentSearchText;
    private String appliedSearchText;

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setHasOptionsMenu(true);
        if (savedInstanceState == null) {
            isSearchOpen = false;
            isSearching = false;
            currentSearchText = "";
            appliedSearchText = "";
        }
    }

    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_main, container, false);

        loadingLayout = view.findViewById(R.id.loading_layout);
        errorLayout = view.findViewById(R.id.error_layout);
        swipeRefreshLayout = view.findViewById(R.id.swipe_to_refresh);
        recyclerView = view.findViewById(R.id.recycler_view);
        emptyLayout = view.findViewById(R.id.empty_layout);

        view.findViewById(R.id.error_button_retry).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                refreshNews(false);
            }
        });

        swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                refreshNews(true);
            }
        });
        swipeRefreshLayout.setColorSchemeResources(R.color.color_primary);

        linearLayoutManager = new LinearLayoutManager(getActivity());
        recyclerView.setLayoutManager(linearLayoutManager);
        adapter = new NewsAdapter(news, new NewsAdapter.NewsListListener() {
            @Override
            public void onNewsItemClicked(News news) {
                if (getActivity() != null) {
                    ((MainActivity) getActivity()).openNews(news);
                }
            }
        });
        recyclerView.setAdapter(adapter);
        recyclerView.addItemDecoration(new DividerItemDecoration(recyclerView.getContext(), DividerItemDecoration.VERTICAL));
        recyclerView.addOnScrollListener(new RecyclerView.OnScrollListener() {
            @Override
            public void onScrolled(RecyclerView recyclerView, int dx, int dy) {
                int pastVisiblesItems, visibleItemCount, totalItemCount;
                if (dy > 0) {
                    visibleItemCount = linearLayoutManager.getChildCount();
                    totalItemCount = linearLayoutManager.getItemCount();
                    pastVisiblesItems = linearLayoutManager.findFirstVisibleItemPosition();

                    if (allowLoadingMoreElements) {
                        if ((visibleItemCount + pastVisiblesItems) >= totalItemCount) {
                            allowLoadingMoreElements = false;
                            currentPage++;
                            loadNews(currentPage, appliedSearchText);
                        }
                    }
                }
            }
        });

        if (savedInstanceState != null) {
            isSearchOpen = savedInstanceState.getBoolean(STATE_IS_SEARCH_OPEN);
            isSearching = savedInstanceState.getBoolean(STATE_IS_SEARCHING);
            currentSearchText = savedInstanceState.getString(STATE_CURRENT_SEARCH_TEXT);
            appliedSearchText = savedInstanceState.getString(STATE_APPLIED_SEARCH_TEXT);
        }

        if (news.isEmpty()) {
            loadingLayout.setVisibility(View.VISIBLE);
            swipeRefreshLayout.setVisibility(View.GONE);
            errorLayout.setVisibility(View.GONE);
            refreshNews(false);
        } else {
            adapter.notifyDataSetChanged();
            loadingLayout.setVisibility(View.GONE);
            swipeRefreshLayout.setVisibility(View.VISIBLE);
            errorLayout.setVisibility(View.GONE);
        }

        allowLoadingMoreElements = true;

        return view;
    }

    public void refreshNews(boolean fromPullToRefresh) {
        if (!fromPullToRefresh) {
            loadingLayout.setVisibility(View.VISIBLE);
            errorLayout.setVisibility(View.GONE);
            swipeRefreshLayout.setVisibility(View.GONE);
        }
        currentPage = 0;
        loadNews(currentPage, appliedSearchText);
    }

    @Override
    public void onSaveInstanceState(@NonNull Bundle outState) {
        super.onSaveInstanceState(outState);
        outState.putBoolean(STATE_IS_SEARCH_OPEN, isSearchOpen);
        outState.putBoolean(STATE_IS_SEARCHING, isSearching);
        outState.putString(STATE_CURRENT_SEARCH_TEXT, currentSearchText);
        outState.putString(STATE_APPLIED_SEARCH_TEXT, appliedSearchText);
    }

    @Override
    public void onResume() {
        super.onResume();
        if (getActivity() != null) {
            if (isSearchOpen) {
                ((MainActivity) getActivity()).setActionViewUpNavigation();
            } else if (isSearching) {
                ((MainActivity) getActivity()).setUpNavigation();
            } else {
                ((MainActivity) getActivity()).setMenuNavigation();
            }
            if (isSearching) {
                getActivity().setTitle(R.string.search_results_title);
            } else {
                getActivity().setTitle(R.string.main_title);
            }
        }
    }

    private void loadNews(final int page, final String searchText) {
        new AsyncTask<Void, Void, List<News>>() {

            @Override
            protected List<News> doInBackground(Void... params) {
                if (!UiUtils.isOnline()) {
                    return null;
                }
                ServerResponse<News> fansubServerResponse = ServerAccess.getNews(page, searchText, DataUtils.retrieveFilterFansubIds());
                if (fansubServerResponse.getStatus().equals(ServerAccess.STATUS_OK)) {
                    return fansubServerResponse.getResult();
                } else {
                    return null;
                }
            }

            @Override
            protected void onPostExecute(List<News> newsResult) {
                if (swipeRefreshLayout.isRefreshing()) {
                    swipeRefreshLayout.setRefreshing(false);
                }
                if (newsResult != null) {
                    if (currentPage == 0) {
                        news.clear();
                    }
                    news.addAll(newsResult);
                    adapter.notifyDataSetChanged();
                    allowLoadingMoreElements = true;
                    loadingLayout.setVisibility(View.GONE);
                    errorLayout.setVisibility(View.GONE);
                    swipeRefreshLayout.setVisibility(View.VISIBLE);
                    if (!news.isEmpty()) {
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
    public void onCreateOptionsMenu(Menu menu, MenuInflater inflater) {
        if (isSearchOpen) {
            inflater.inflate(R.menu.menu_main_expanded, menu);
            MenuItem expanded = menu.findItem(R.id.menu_search_expanded);
            expanded.setVisible(true);

            final EditText searchEditText = expanded.getActionView().findViewById(R.id.action_search_text);
            if (!TextUtils.isEmpty(currentSearchText)) {
                searchEditText.setText(currentSearchText);
            } else {
                searchEditText.setText(appliedSearchText);
            }
            ImageView clearButton = expanded.getActionView().findViewById(R.id.action_search_clear);
            searchEditText.setImeActionLabel(getString(R.string.search_go_button), EditorInfo.IME_ACTION_SEARCH);
            searchEditText.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence s, int start, int count, int after) {
                    //Nothing
                }

                @Override
                public void onTextChanged(CharSequence s, int start, int before, int count) {
                    //Nothing
                }

                @Override
                public void afterTextChanged(Editable s) {
                    currentSearchText = s.toString();
                }
            });
            searchEditText.setOnEditorActionListener(new TextView.OnEditorActionListener() {
                @Override
                public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
                    if (v.getText().length() < 2) {
                        Toast.makeText(getActivity(), R.string.search_please_enter, Toast.LENGTH_SHORT).show();
                    } else {
                        UiUtils.hideKeyboard(v);
                        closeSearch(true);
                        if (getActivity() != null) {
                            ((MainActivity) getActivity()).setUpNavigation();
                        }
                    }
                    return true;
                }
            });
            clearButton.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    searchEditText.setText("");
                }
            });
            UiUtils.requestFocusAndShowKeyboard(searchEditText);
        } else {
            inflater.inflate(R.menu.menu_main, menu);
        }
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case R.id.action_search:
                openSearch();
                return true;
            case R.id.action_filter:
                FilterDialogFragment fd = new FilterDialogFragment();
                fd.show(getFragmentManager(), FilterDialogFragment.TAG);
                return true;
        }
        return super.onOptionsItemSelected(item);
    }

    private void openSearch() {
        if (getActivity() != null) {
            isSearchOpen = true;
            getActivity().invalidateOptionsMenu();
            ((MainActivity) getActivity()).setActionViewUpNavigation();
        }
    }

    public void closeSearch(boolean openingResult) {
        if (getActivity() != null) {
            UiUtils.hideKeyboard(loadingLayout);
            isSearchOpen = false;
            getActivity().invalidateOptionsMenu();
            ((MainActivity) getActivity()).onSearchViewCollapsed(openingResult, TextUtils.isEmpty(appliedSearchText));

            if (openingResult) {
                isSearching = true;
                appliedSearchText = currentSearchText;
                currentSearchText = "";
                getActivity().setTitle(R.string.search_results_title);
                refreshNews(false);
            }
        }
    }

    @Override
    public boolean onBackPressed() {
        if (getActivity() != null) {
            if (isSearching) {
                isSearching = false;
                appliedSearchText = "";
                refreshNews(false);
                getActivity().setTitle(R.string.main_title);
                ((MainActivity) getActivity()).setMenuNavigation();
                return true;
            }
        }
        return false;
    }

    public boolean isSearching() {
        return isSearching;
    }
}
