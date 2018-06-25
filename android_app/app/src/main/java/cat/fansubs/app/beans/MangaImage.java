package cat.fansubs.app.beans;

import java.io.Serializable;

public class MangaImage implements Serializable {
    private Long id;
    private String name;
    private String elementUrl;

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getElementUrl() {
        return elementUrl;
    }

    public void setElementUrl(String elementUrl) {
        this.elementUrl = elementUrl;
    }
}
