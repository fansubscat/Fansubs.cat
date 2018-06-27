package cat.fansubs.app.components;

import android.text.style.ClickableSpan;
import android.view.View;

import cat.fansubs.app.utils.UiUtils;

/**
 * Custom ClickableSpan that launches the WebView activity instead of the Android browser
 */
public class CustomURLSpan extends ClickableSpan {

    private final String mURL;

    public CustomURLSpan(String url) {
        mURL = url;
    }

    public String getURL() {
        return mURL;
    }

    @Override
    public void onClick(View widget) {
        UiUtils.openUrl(widget.getContext(), getURL());
    }
}
