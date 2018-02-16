package cat.fansubs.app.fragments;

import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.widget.DividerItemDecoration;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import cat.fansubs.app.R;
import cat.fansubs.app.activities.MainActivity;
import cat.fansubs.app.adapters.FansubsAdapter;
import cat.fansubs.app.beans.Fansub;
import cat.fansubs.app.utils.DataUtils;

public class FansubsFragment extends Fragment implements BackableFragment {

    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_fansubs, container, false);

        RecyclerView recyclerView = view.findViewById(R.id.recycler_view);
        LinearLayoutManager linearLayoutManager = new LinearLayoutManager(getActivity());
        recyclerView.setLayoutManager(linearLayoutManager);

        List<Fansub> fansubs = new ArrayList<>(DataUtils.retrieveFansubs());

        for (Iterator<Fansub> iterator = fansubs.iterator(); iterator.hasNext(); ) {
            Fansub fansub = iterator.next();
            if (fansub.isOwn() || !fansub.isVisible()) {
                iterator.remove();
            }
        }

        FansubsAdapter adapter = new FansubsAdapter(fansubs, new FansubsAdapter.FansubsListListener() {
            @Override
            public void onFansubItemClicked(Fansub fansub) {
                if (getActivity() != null) {
                    ((MainActivity) getActivity()).openFansub(fansub);
                }
            }
        });
        recyclerView.setAdapter(adapter);
        recyclerView.addItemDecoration(new DividerItemDecoration(recyclerView.getContext(), DividerItemDecoration.VERTICAL));
        return view;
    }

    @Override
    public void onResume() {
        super.onResume();
        if (getActivity() != null) {
            getActivity().setTitle(R.string.fansubs_title);
        }
    }

    @Override
    public boolean onBackPressed() {
        return false;
    }
}
