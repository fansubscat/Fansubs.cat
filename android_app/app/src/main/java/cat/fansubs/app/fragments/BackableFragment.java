package cat.fansubs.app.fragments;

public interface BackableFragment {
    /**
     * Allows intercepting the back button.
     * @return false if the back press must proceed, true if we intercept it.
     */
    boolean onBackPressed();
}
