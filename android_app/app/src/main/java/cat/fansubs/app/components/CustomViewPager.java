package cat.fansubs.app.components;

import android.content.Context;
import android.support.v4.view.ViewPager;
import android.util.AttributeSet;

public class CustomViewPager extends ViewPager {

    public CustomViewPager(Context context) {
        super(context);
    }

    public CustomViewPager(Context context, AttributeSet attrs) {
        super(context, attrs);
    }

//    @Override
//    protected boolean canScroll(View v, boolean checkV, int dx, int x, int y) {
//        if (v instanceof ImageViewTouch) {
//            boolean result = ((ImageViewTouch) v).canScroll(dx);
//            if (!result) {
//                ((ImageViewTouch) v).resetDisplay();
//            }
//            return result;
//        } else {
//            return super.canScroll(v, checkV, dx, x, y);
//        }
//    }
}
