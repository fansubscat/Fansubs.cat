package cat.fansubs.app.components;

import android.content.Context;
import android.util.AttributeSet;
import android.view.MotionEvent;
import android.view.View;

import it.sephiroth.android.library.imagezoom.ImageViewTouch;

public class CustomImageViewTouch extends ImageViewTouch {
    public CustomImageViewTouch(Context context, AttributeSet attrs) {
        super(context, attrs);
        init();
    }

    public CustomImageViewTouch(Context context, AttributeSet attrs, int defStyle) {
        super(context, attrs, defStyle);
        init();
    }

    private void init() {
        setDisplayType(DisplayType.FIT_TO_SCREEN);
        setOnTouchListener(new OnTouchListener() {
            @Override
            public boolean onTouch(View v, MotionEvent event) {
                if (getScale() > 1f) {
                    getParent().requestDisallowInterceptTouchEvent(true);
                } else {
                    getParent().requestDisallowInterceptTouchEvent(false);
                }
                return false;
            }
        });
    }
}
