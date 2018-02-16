package cat.fansubs.app.beans;

public class UnreadPush {
    private String fansub;
    private String title;
    private String fansubId;

    public UnreadPush(String fansub, String title, String fansubId) {
        this.fansub = fansub;
        this.title = title;
        this.fansubId = fansubId;
    }

    public String getFansub() {
        return fansub;
    }

    public void setFansub(String fansub) {
        this.fansub = fansub;
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getFansubId() {
        return fansubId;
    }

    public void setFansubId(String fansubId) {
        this.fansubId = fansubId;
    }
}
