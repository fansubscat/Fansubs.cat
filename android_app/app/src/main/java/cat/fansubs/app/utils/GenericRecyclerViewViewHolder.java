package cat.fansubs.app.utils;

import android.support.v7.widget.RecyclerView;
import android.view.View;

import java.util.Hashtable;

public class GenericRecyclerViewViewHolder extends RecyclerView.ViewHolder {
    private Hashtable<String, View> holder = new Hashtable<>();

    public GenericRecyclerViewViewHolder(View itemView) {
        super(itemView);
    }

    public void setView(String k, View v) {
        holder.put(k, v);
    }

    public View getView(String k) {
        return holder.get(k);
    }

    public <T> T getView(String k, Class<T> type) {
        return type.cast(getView(k));
    }
}
