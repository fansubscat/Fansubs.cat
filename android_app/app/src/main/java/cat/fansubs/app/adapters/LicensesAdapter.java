package cat.fansubs.app.adapters;

import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.TextView;

import java.util.List;

import cat.fansubs.app.R;
import cat.fansubs.app.beans.License;
import cat.fansubs.app.utils.UiUtils;

public class LicensesAdapter extends RecyclerView.Adapter<LicensesAdapter.LicensesViewHolder> {
    private List<License> licensesList;

    public LicensesAdapter(List<License> licensesList) {
        this.licensesList = licensesList;
    }

    @Override
    public LicensesViewHolder onCreateViewHolder(ViewGroup viewGroup, int viewType) {
        View itemView = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_license, viewGroup, false);
        return new LicensesViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(LicensesViewHolder viewHolder, int position) {
        License item = licensesList.get(position);
        viewHolder.name.setText(item.getName());
        if (item.getCopyright() != null) {
            viewHolder.copyright.setVisibility(View.VISIBLE);
            viewHolder.copyright.setText(item.getCopyright());
        } else {
            viewHolder.copyright.setVisibility(View.GONE);
        }
        if (item.getLicense() != null) {
            viewHolder.license.setVisibility(View.VISIBLE);
            viewHolder.license.setText(item.getLicense());
        } else {
            viewHolder.license.setVisibility(View.GONE);
        }
        if (item.getUrl() != null) {
            viewHolder.link.setVisibility(View.VISIBLE);
        } else {
            viewHolder.link.setVisibility(View.GONE);
        }
        viewHolder.itemView.setTag(item);
    }

    @Override
    public int getItemCount() {
        return licensesList.size();
    }

    class LicensesViewHolder extends RecyclerView.ViewHolder {
        private TextView name;
        private TextView copyright;
        private TextView license;
        private ImageButton link;

        LicensesViewHolder(View view) {
            super(view);
            name = view.findViewById(R.id.license_name);
            copyright = view.findViewById(R.id.license_copyright);
            license = view.findViewById(R.id.license_text);
            link = view.findViewById(R.id.license_url);

            link.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    UiUtils.openUrl(v.getContext(), ((License) itemView.getTag()).getUrl());
                }
            });
        }
    }
}
