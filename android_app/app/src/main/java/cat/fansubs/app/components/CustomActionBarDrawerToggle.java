package cat.fansubs.app.components;

import android.animation.ArgbEvaluator;
import android.animation.ValueAnimator;
import android.graphics.PorterDuff;
import android.support.v4.content.ContextCompat;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.widget.Toolbar;
import android.view.MenuItem;
import android.view.View;
import android.view.animation.DecelerateInterpolator;

import cat.fansubs.app.R;
import cat.fansubs.app.activities.MainActivity;

//Taken from http://stackoverflow.com/a/28203304/1254846
//...and heavily modified
public class CustomActionBarDrawerToggle extends ActionBarDrawerToggle {
    private static final float MENU_POSITION = 0f;
    private static final float ARROW_POSITION = 1.0f;
    private static final float MAX_SEARCH_HELP_LAYOUT_ALPHA = 0.4f;

    private final int animationLength;
    private final DrawerLayout drawerLayout;
    private final MainActivity activity;
    private final Toolbar toolbar;
    private final View searchHelpLayout;
    private State currentState;
    private final Object lock = new Object();
    private boolean isProgrammaticSlide = false;

    public enum State {UP, ACTIONVIEW_UP, MENU}

    public CustomActionBarDrawerToggle(MainActivity activity, DrawerLayout drawerLayout, int openDrawerContentDescriptionResource, int closeDrawerContentDescriptionResource, Toolbar toolbar, View searchHelpLayout) {
        super(activity, drawerLayout, openDrawerContentDescriptionResource, closeDrawerContentDescriptionResource);
        animationLength = activity.getResources().getInteger(android.R.integer.config_shortAnimTime);
        this.drawerLayout = drawerLayout;
        this.activity = activity;
        this.toolbar = toolbar;
        this.searchHelpLayout = searchHelpLayout;
        this.searchHelpLayout.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                CustomActionBarDrawerToggle.this.activity.onActionViewClosed();
            }
        });
        currentState = State.MENU;
        syncState();
    }

    public void animateToState(State state) {
        if (state != currentState) {
            State previousState = currentState;
            currentState = state;

            if (state == State.UP || state == State.ACTIONVIEW_UP) {
                if (previousState == State.MENU) {
                    onDrawerOpened(drawerLayout);
                    ValueAnimator anim = ValueAnimator.ofFloat(MENU_POSITION, ARROW_POSITION);
                    anim.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
                        @Override
                        public void onAnimationUpdate(ValueAnimator valueAnimator) {
                            float slideOffset = (Float) valueAnimator.getAnimatedValue();
                            synchronized (lock) {
                                isProgrammaticSlide = true;
                                onDrawerSlide(drawerLayout, slideOffset);
                                isProgrammaticSlide = false;
                            }
                        }
                    });

                    anim.setInterpolator(new DecelerateInterpolator());
                    anim.setDuration(animationLength);
                    anim.start();
                }
                drawerLayout.setDrawerLockMode(DrawerLayout.LOCK_MODE_LOCKED_CLOSED);
                onDrawerOpened(drawerLayout);
                if (currentState == State.ACTIONVIEW_UP) {
                    fadeToolbarToActionViewMode();
                } else if (previousState == State.ACTIONVIEW_UP) {
                    fadeToolbarToNormalMode();
                }
            } else if (state == State.MENU) {
                onDrawerClosed(drawerLayout);
                ValueAnimator anim = ValueAnimator.ofFloat(ARROW_POSITION, MENU_POSITION);
                anim.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
                    @Override
                    public void onAnimationUpdate(ValueAnimator valueAnimator) {
                        float slideOffset = (Float) valueAnimator.getAnimatedValue();
                        synchronized (lock) {
                            isProgrammaticSlide = true;
                            onDrawerSlide(drawerLayout, slideOffset);
                            isProgrammaticSlide = false;
                        }
                    }
                });

                anim.setInterpolator(new DecelerateInterpolator());
                anim.setDuration(animationLength);
                anim.start();
                drawerLayout.setDrawerLockMode(DrawerLayout.LOCK_MODE_UNLOCKED);
                onDrawerClosed(drawerLayout);
                if (previousState == State.ACTIONVIEW_UP) {
                    fadeToolbarToNormalMode();
                }
            }
        }
    }

    private void fadeToolbarToActionViewMode() {
        searchHelpLayout.setVisibility(View.VISIBLE);
        searchHelpLayout.setAlpha(0f);
        int colorFrom = ContextCompat.getColor(activity, R.color.toolbar_normal_bg_color);
        int colorTo = ContextCompat.getColor(activity, R.color.toolbar_actionview_bg_color);
        ValueAnimator colorAnimation = ValueAnimator.ofObject(new ArgbEvaluator(), colorFrom, colorTo);
        colorAnimation.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
            @Override
            public void onAnimationUpdate(ValueAnimator animator) {
                toolbar.setBackgroundColor((int) animator.getAnimatedValue());
            }

        });
        colorAnimation.start();
        int colorFromArrow = ContextCompat.getColor(activity, R.color.toolbar_normal_fg_color);
        int colorToArrow = ContextCompat.getColor(activity, R.color.toolbar_actionview_fg_color);
        ValueAnimator colorAnimationArrow = ValueAnimator.ofObject(new ArgbEvaluator(), colorFromArrow, colorToArrow);
        colorAnimationArrow.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
            @Override
            public void onAnimationUpdate(ValueAnimator animator) {
                searchHelpLayout.setAlpha(MAX_SEARCH_HELP_LAYOUT_ALPHA * animator.getAnimatedFraction());
                if (toolbar.getNavigationIcon()!=null) {
                    toolbar.getNavigationIcon().setColorFilter((int) animator.getAnimatedValue(), PorterDuff.Mode.SRC_ATOP);
                }
            }

        });
        colorAnimationArrow.start();
    }

    private void fadeToolbarToNormalMode() {
        int colorFrom = ContextCompat.getColor(activity, R.color.toolbar_actionview_bg_color);
        int colorTo = ContextCompat.getColor(activity, R.color.toolbar_normal_bg_color);
        ValueAnimator colorAnimation = ValueAnimator.ofObject(new ArgbEvaluator(), colorFrom, colorTo);
        colorAnimation.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
            @Override
            public void onAnimationUpdate(ValueAnimator animator) {
                toolbar.setBackgroundColor((int) animator.getAnimatedValue());
            }

        });
        colorAnimation.start();

        int colorFromArrow = ContextCompat.getColor(activity, R.color.toolbar_actionview_fg_color);
        int colorToArrow = ContextCompat.getColor(activity, R.color.toolbar_normal_fg_color);
        ValueAnimator colorAnimationArrow = ValueAnimator.ofObject(new ArgbEvaluator(), colorFromArrow, colorToArrow);
        colorAnimationArrow.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
            @Override
            public void onAnimationUpdate(ValueAnimator animator) {
                searchHelpLayout.setAlpha(MAX_SEARCH_HELP_LAYOUT_ALPHA * (1 - animator.getAnimatedFraction()));
                if (animator.getAnimatedFraction() == 1f) {
                    searchHelpLayout.setVisibility(View.GONE);
                }
                if (toolbar.getNavigationIcon() != null) {
                    toolbar.getNavigationIcon().setColorFilter((int) animator.getAnimatedValue(), PorterDuff.Mode.SRC_ATOP);
                }
            }

        });
        colorAnimationArrow.start();
    }

    public State getCurrentState() {
        return currentState;
    }

    public void setCurrentState(State state) {
        if (state == State.UP) {
            drawerLayout.setDrawerLockMode(DrawerLayout.LOCK_MODE_UNLOCKED);
            onDrawerOpened(drawerLayout);
            toolbar.setBackgroundColor(ContextCompat.getColor(activity, R.color.toolbar_normal_bg_color));
            if (toolbar.getNavigationIcon() != null) {
                toolbar.getNavigationIcon().setColorFilter(ContextCompat.getColor(activity, R.color.toolbar_normal_fg_color), PorterDuff.Mode.SRC_ATOP);
            }
            searchHelpLayout.setVisibility(View.GONE);
            searchHelpLayout.setAlpha(0f);
        } else if (state == State.ACTIONVIEW_UP) {
            onDrawerOpened(drawerLayout);
            drawerLayout.setDrawerLockMode(DrawerLayout.LOCK_MODE_LOCKED_CLOSED);
            toolbar.setBackgroundColor(ContextCompat.getColor(activity, R.color.toolbar_actionview_bg_color));
            if (toolbar.getNavigationIcon() != null) {
                toolbar.getNavigationIcon().setColorFilter(ContextCompat.getColor(activity, R.color.toolbar_actionview_fg_color), PorterDuff.Mode.SRC_ATOP);
            }
            searchHelpLayout.setVisibility(View.VISIBLE);
            searchHelpLayout.setAlpha(MAX_SEARCH_HELP_LAYOUT_ALPHA);
        } else {
            onDrawerClosed(drawerLayout);
            drawerLayout.setDrawerLockMode(DrawerLayout.LOCK_MODE_UNLOCKED);
            toolbar.setBackgroundColor(ContextCompat.getColor(activity, R.color.toolbar_normal_bg_color));
            if (toolbar.getNavigationIcon() != null) {
                toolbar.getNavigationIcon().setColorFilter(ContextCompat.getColor(activity, R.color.toolbar_normal_fg_color), PorterDuff.Mode.SRC_ATOP);
            }
            searchHelpLayout.setVisibility(View.GONE);
            searchHelpLayout.setAlpha(0f);
        }

        currentState = state;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
            case (android.R.id.home):
                if (currentState == State.UP || currentState == State.ACTIONVIEW_UP) {
                    activity.onBackPressed();
                    return true;
                }
        }
        return super.onOptionsItemSelected(item);
    }

    @Override
    public void onDrawerSlide(View drawerView, float slideOffset) {
        if (isProgrammaticSlide) {
            super.onDrawerSlide(drawerView, slideOffset);
        } else if (currentState == State.UP) {
            super.onDrawerOpened(drawerView);
        } else {
            super.onDrawerClosed(drawerView);
        }
    }

    @Override
    public void onDrawerClosed(View drawerView) {
        super.onDrawerClosed(drawerView);
        if (currentState == State.UP) {
            onDrawerOpened(drawerView);
        }
    }
}