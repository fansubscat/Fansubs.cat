package cat.fansubs.app.serveraccess.model;

import java.util.List;

import cat.fansubs.app.beans.MangaImage;

public class PiwigoImagesResponse {
    private List<MangaImage> images;

    public List<MangaImage> getImages() {
        return images;
    }

    public void setImages(List<MangaImage> images) {
        this.images = images;
    }
}
