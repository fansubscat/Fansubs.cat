package cat.fansubs.app.serveraccess.model;

import java.util.List;

import cat.fansubs.app.beans.MangaCategory;

public class PiwigoCategoriesResponse {
    private List<MangaCategory> categories;

    public List<MangaCategory> getCategories() {
        return categories;
    }

    public void setCategories(List<MangaCategory> categories) {
        this.categories = categories;
    }
}
