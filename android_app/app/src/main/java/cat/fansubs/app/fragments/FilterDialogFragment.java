package cat.fansubs.app.fragments;

import android.app.Dialog;
import android.graphics.Point;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.v4.app.DialogFragment;
import android.support.v7.app.AlertDialog;
import android.support.v7.widget.SwitchCompat;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.CompoundButton;
import android.widget.ImageView;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.request.RequestOptions;

import java.util.ArrayList;
import java.util.List;

import cat.fansubs.app.R;
import cat.fansubs.app.activities.MainActivity;
import cat.fansubs.app.beans.Fansub;
import cat.fansubs.app.utils.DataUtils;

public class FilterDialogFragment extends DialogFragment {
    public static final String TAG = "FilterDialogFragment";

    private ViewGroup fansubsContainer;
    private List<String> filterFansubIds;

    @NonNull
    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(getActivity(), R.style.FilterDialogTheme);
        alertDialogBuilder.setCancelable(false);

        final AlertDialog alertDialog = alertDialogBuilder.create();
        View view = View.inflate(getContext(), R.layout.dialog_filter, null);
        alertDialog.setView(view, 0, 0, 0, 0);
        Point size = new Point();
        if (alertDialog.getWindow() != null) {
            alertDialog.getWindow().getWindowManager().getDefaultDisplay().getSize(size);

            WindowManager.LayoutParams params = alertDialog.getWindow().getAttributes();
            params.gravity = Gravity.TOP | Gravity.START;
            params.x = 0;
            params.y = 0;
            alertDialog.getWindow().setAttributes(params);
        }

        fansubsContainer = view.findViewById(R.id.filter_fansubs_container);

        filterFansubIds = DataUtils.retrieveFilterFansubIds();

        view.findViewById(R.id.filter_close).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                dismiss();
            }
        });
        view.findViewById(R.id.filter_apply).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (filterFansubIds.size() == DataUtils.retrieveFansubs().size()) {
                    //We save no filter at all so new fansubs are shown
                    DataUtils.storeFilterFansubIds(new ArrayList<String>());
                } else {
                    DataUtils.storeFilterFansubIds(filterFansubIds);
                }
                ((MainActivity) getActivity()).onFilterChanged();
                dismiss();
            }
        });

        updateFansubs();

        return alertDialog;
    }

    private void updateFansubs() {
        fansubsContainer.removeAllViews();
        boolean first = true;

        if (filterFansubIds.isEmpty()) {
            for (final Fansub fansub : DataUtils.retrieveFansubs()) {
                filterFansubIds.add(fansub.getId());
            }
        }

        for (final Fansub fansub : DataUtils.retrieveFansubs()) {
            if (!first) {
                View.inflate(getContext(), R.layout.generic_divider_gray, fansubsContainer);
            } else {
                first = false;
            }

            boolean isSelected = filterFansubIds.isEmpty() || filterFansubIds.contains(fansub.getId());

            View view = LayoutInflater.from(getContext()).inflate(R.layout.item_fansub_filter, fansubsContainer, false);

            ((TextView) view.findViewById(R.id.fansub_name)).setText(fansub.getName());
            ImageView fansubIcon = view.findViewById(R.id.fansub_image);
            Glide.with(fansubIcon.getContext()).load(fansub.getIconUrl())
                    .apply(new RequestOptions().placeholder(R.color.transparent).error(R.color.transparent))
                    .into(fansubIcon);
            SwitchCompat fansubEnabled = view.findViewById(R.id.fansub_enabled);

            fansubEnabled.setChecked(isSelected);
            fansubEnabled.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
                @Override
                public void onCheckedChanged(CompoundButton compoundButton, boolean b) {
                    if (b) {
                        filterFansubIds.add(fansub.getId());
                    } else {
                        filterFansubIds.remove(fansub.getId());
                    }
                }
            });

            fansubsContainer.addView(view);
        }
    }
}
