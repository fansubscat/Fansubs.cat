package cat.fansubs.app.beans;

import java.io.Serializable;

public class News implements Serializable {
    private long date;
    private String fansubId;
    private String fansubName;
    private String title;
    private String contents;
    private String url;
    private String imageUrl;

    public long getDate() {
        return date;
    }

    public void setDate(long date) {
        this.date = date;
    }

    public String getFansubId() {
        return fansubId;
    }

    public void setFansubId(String fansubId) {
        this.fansubId = fansubId;
    }

    public String getFansubName() {
        return fansubName;
    }

    public void setFansubName(String fansubName) {
        this.fansubName = fansubName;
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getContents() {
        return contents;
    }

    public void setContents(String contents) {
        this.contents = contents;
    }

    public String getUrl() {
        return url;
    }

    public void setUrl(String url) {
        this.url = url;
    }

    public String getImageUrl() {
        return imageUrl;
    }

    public void setImageUrl(String imageUrl) {
        this.imageUrl = imageUrl;
    }
}
